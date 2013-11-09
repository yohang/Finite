<?php

namespace Finite\Visualisation;

use Finite\StateMachine\StateMachineInterface;
use Finite\State\StateInterface;
use Finite\Transition\TransitionInterface;
use Alom\Graphviz\Digraph;

/**
 * Visualisation of a State machine using Graphviz
 *
 *
 * @link http://www.graphviz.org/Gallery/directed/fsm.gv.txt
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Graphviz
{
    private $executable = 'dot';
    private $type = 'png';
    
    /**
     * the graphviz graph representation
     * 
     * @var \Alom\Graphviz\Digraph
     */
    private $graph;
    
    /**
     * Renders the state machine.
     * 
     * @param \Finite\StateMachine\StateMachineInterface $stateMachine
     * @param string $target
     */
    public function render(StateMachineInterface $stateMachine, $target)
    {
        $this->createGraph();
        $this->addNodes($stateMachine);
        $this->addEdges($stateMachine);
        
        $this->graph->end();
        file_put_contents($target, $this->graph->render());
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
            $state = $stateMachine->getState($name);
            /* @var $state \Finite\State\StateInterface */
            $shape = $state->getType() != StateInterface::TYPE_NORMAL ? 'doublecircle' : 'circle';
            $id       = $state->getName();
            $this->graph->beginNode($id, array('shape' => $shape))->end();
        }
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
                    array($state->getName(), $trans->getState()), 
                    array('label' => $trans->getName()))
                    ->end();
            }
        }
    }
    
    /**
     * 
     * @return 
     */
    private function createGraph()
    {
        $this->graph = new \Alom\Graphviz\Digraph('state_machine');
    }
}