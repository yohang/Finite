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
    /**
     * target format
     * 
     * @var string
     */
    private $type = 'dot';
    
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
     * @throws Exception
     */
    public function render(StateMachineInterface $stateMachine, $target)
    {
        $this->graph = new \Alom\Graphviz\Digraph('state_machine');
        $this->addNodes($stateMachine);
        $this->addEdges($stateMachine);
        
        $this->finalize($target);
    }
    
    /**
     * Guesses the target format based on the extension.
     * 
     * @param string $target
     */
    private function finalize($target)
    {
        $this->graph->end();
        
        $this->type = pathinfo($target, PATHINFO_EXTENSION);
        if ($this->type != 'dot') {
            $this->renderDot($target);
        } else {
            $this->dumpGraphToFile($target);
        }
    }
    
    /**
     * Executes dot
     * 
     * @param string $target
     * @throws Exception
     */
    private function renderDot($target)
    {
        $returnVar = 0;
        $output = 0;
        $tempFile = tempnam(sys_get_temp_dir(), 'dot');
        $this->dumpGraphToFile($tempFile);
        exec('dot -T' . $this->type. ' -o' . $target . ' ' . $tempFile, $output, $returnVar);
        if ($returnVar > 0) {
            throw new Exception('Error executing dot.', Exception::CODE_DOT_ERROR);
        }
    }
    
    /**
     * Write the raw dot content to the given file.
     * 
     * @param string $file
     * @throws Exception
     */
    private function dumpGraphToFile($file)
    {
        if (!file_put_contents($file, $this->graph->render())) {
            throw new Exception('Error dumping the dot content to ' . $file, 500);
        }
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
}