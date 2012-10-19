<?php

namespace Finite\Loader;

use Finite\StatefulInterface;
use  Finite\StateMachine\StateMachine;
use Finite\State\State;
use Finite\Transition\Transition;

/**
 * Loads a StateMachine from an array
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ArrayLoader implements LoaderInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge(
            array(
                'class'       => '',
                'states'      => array(),
                'transitions' => array(),
            ),
            $config
        );
    }

    /**
     * @{inheritDoc}
     */
    public function load(StateMachine $stateMachine)
    {
        $this->loadStates($stateMachine);
        $this->loadTransitions($stateMachine);
    }

    /**
     * @{inheritDoc}
     */
    public function supports(StatefulInterface $object)
    {
        $reflection = new \ReflectionClass($this->config['class']);

        return $reflection->isInstance($object);
    }

    /**
     * @param StateMachine $stateMachine
     */
    private function loadStates(StateMachine $stateMachine)
    {
        foreach ($this->config['states'] as $state => $config) {
            $stateMachine->addState(new State($state, $config['type'], array(), $config['properties']));
        }
    }

    /**
     * @param StateMachine $stateMachine
     */
    private function loadTransitions(StateMachine $stateMachine)
    {
        foreach ($this->config['transitions'] as $transition => $config) {
            $stateMachine->addTransition(new Transition($transition, $config['from'], $config['to']));
        }
    }
}
