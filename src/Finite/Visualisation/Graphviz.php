<?php

namespace Finite\Visualisation;

use Finite\StateMachine\StateMachineInterface;
use Finite\State\StateInterface;
use Alom\Graphviz\Digraph;

/**
 * Visualisation of a State machine using Graphviz
 *
 * This class geneates dot source code which can be rendered
 * by graphviz. Pass a configuration object to control how
 * the nodes are rendered.
 *
 * @link http://www.graphviz.org/Gallery/directed/fsm.gv.txt
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Graphviz implements VisualisationInterface
{
    /**
     * the graphviz graph representation
     *
     * @var \Alom\Graphviz\Digraph
     */
    private $graph;

    /**
     * visualisation options
     *
     * @var \Finite\Visualisation\Configuration
     */
    private $configuration;

    /**
     * Constructor.
     *
     * @param \Finite\Visualisation\Configuration $config
     */
    public function __construct(Configuration $config = null)
    {
        if (null === $config) {
            $config = new Configuration();
        }
        $this->configuration = $config;
    }

    /**
     * Renders the state machine.
     *
     * @param  \Finite\StateMachine\StateMachineInterface $stateMachine
     * @param  string                                     $target
     * @throws Exception
     */
    public function render(StateMachineInterface $stateMachine)
    {
        $this->graph = new Digraph('state_machine');
        $this->addNodes($stateMachine);
        $this->addEdges($stateMachine);
        $this->graph->end();

        return $this->graph->render();
    }

    /**
     * Adds the states as nodes.
     *
     * @param \Finite\StateMachine\StateMachineInterface $stateMachine
     */
    private function addNodes(StateMachineInterface $stateMachine)
    {
        $states = $stateMachine->getStates();
        foreach ($states as $name) {
            $this->graph->beginNode($name, $this->getNodeAttributes($stateMachine, $name))->end();
        }
    }

    /**
     * Returns the node attributes.
     *
     * @param  \Finite\StateMachine\StateMachineInterface $stateMachine
     * @param  string                                     $name
     * @return array
     */
    private function getNodeAttributes(StateMachineInterface $stateMachine, $name)
    {
        $state = $stateMachine->getState($name); /* @var $state \Finite\State\StateInterface */
        $data  = array(
            'shape' => $state->getType() != StateInterface::TYPE_NORMAL ? 'doublecircle' : 'circle',
            'label' => $this->getNodeLabel($state),
        );
        if ($stateMachine->getCurrentState() == $state && $this->configuration->markCurrentState()) {
            $data['fillcolor'] = $this->configuration->markCurrentState();
            $data['style'] = 'filled';
        }

        return $data;
    }

    /**
     * Returns the node label.
     *
     * @param  \Finite\State\StateInterface $state
     * @return string
     */
    private function getNodeLabel(StateInterface $state)
    {
        $id = $state->getName();
        $props = $state->getProperties();
        if (count($props) > 0 && $this->configuration->renderProperties()) {
            foreach (array_keys($props) as $prop) {
                $id .= "\\n* " . $prop;
            }
        }

        return $id;
    }

    /**
     * Adds all transitions as edges.
     *
     * @param \Finite\StateMachine\StateMachineInterface $stateMachine
     */
    private function addEdges(StateMachineInterface $stateMachine)
    {
        $states = $stateMachine->getStates();
        foreach ($states as $name) {
            $state = $stateMachine->getState($name);
            /* @var $state \Finite\State\StateInterface */
            $transitions = $state->getTransitions();
            foreach ($transitions as $name) {
                $trans = $stateMachine->getTransition($name);
                /* @var $trans Finite\Transition\TransitionInterface */
                $this->graph->beginEdge(
                        array($state->getName(), $trans->getState()), array('label' => $trans->getName()))
                    ->end();
            }
        }
    }

}
