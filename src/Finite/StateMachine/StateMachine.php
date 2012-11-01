<?php

namespace Finite\StateMachine;

use Finite\Exception;
use Finite\StatefulInterface;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\Transition\Transition;
use Finite\Transition\TransitionInterface;

/**
 * The Finite State Machine
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachine implements StateMachineInterface
{
    /**
     * The stateful object
     *
     * @var StatefulInterface
     */
    public $object;

    /**
     * The available states
     *
     * @var array
     */
    public $states = array();

    /**
     * The available transitions
     *
     * @var array
     */
    public $transitions = array();

    /**
     * The current state
     *
     * @var StateInterface
     */
    public $currentState;

    /**
     * @param StatefulInterface $object
     */
    public function __construct(StatefulInterface $object = null)
    {
        $this->object = $object;
    }

    /**
     * @{inheritDoc}
     */
    public function initialize()
    {
        $this->currentState = $this->getState($this->object->getFiniteState());
    }

    /**
     * @{inheritDoc}
     *
     * @throws Exception\StateException
     */
    public function apply($transitionName)
    {
        $transition = $this->getTransition($transitionName);
        if (!$this->currentState->can($transition)) {
            throw new Exception\StateException(sprintf(
               'The "%s" transition can not be applied to the "%s" state.',
                $transition->getName(),
                $this->currentState->getName()
            ));
        }

        $returnValue = $transition->process($this);

        $this->object->setFiniteState($transition->getState());
        $this->currentState = $this->getState($transition->getState());

        return $returnValue;
    }

    /**
     * @{inheritDoc}
     */
    public function can($transition)
    {
        return $this->currentState->can($transition);
    }

    /**
     * @{inheritDoc}
     */
    public function addState($state)
    {
        if (!$state instanceof StateInterface) {
            $state = new State($state);
        }

        $this->states[$state->getName()] = $state;
    }

    /**
     * @{inheritDoc}
     */
    public function addTransition($transition, $initialState = null, $finalState = null)
    {
        if ((null === $initialState || null === $finalState) && !$transition instanceof TransitionInterface) {
            throw new \InvalidArgumentException(
                'You must provide a TransitionInterface instance or the $transition, '.
                '$initialState and $finalState parameters'
            );
        }
        // If transition isn't a TransitionInterface instance, we create one from the states date
        if (!$transition instanceof TransitionInterface) {
            try {
                $transition = $this->getTransition($transition);
            } catch (Exception\TransitionException $e) {
                $transition = new Transition($transition, $initialState, $finalState);
            }
        }

        $this->transitions[$transition->getName()] = $transition;

        // We add missings states to the State Machine
        try {
            $this->getState($transition->getState());
        } catch (Exception\StateException $e) {
            $this->addState($transition->getState());
        }
        foreach ($transition->getInitialStates() as $state) {
            try {
                $this->getState($state);
            } catch (Exception\StateException $e) {
                $this->addState($state);
            }
            $state = $this->getState($state);
            if ($state instanceof State) {
                $state->addTransition($transition);
            }
        }
    }

    /**
     * @{inheritDoc}
     */
    public function getTransition($name)
    {
        if (!isset($this->transitions[$name])) {
            throw new Exception\TransitionException('Unable to find a transition called '.$name);
        }

        return $this->transitions[$name];
    }

    /**
     * @{inheritDoc}
     */
    public function getState($name)
    {
        if (!isset($this->states[$name])) {
            throw new Exception\StateException('Unable to find a state called '.$name);
        }

        return $this->states[$name];
    }

    /**
     * @{inheritDoc}
     */
    public function getTransitions()
    {
        return array_keys($this->transitions);
    }

    /**
     * @{inheritDoc}
     */
    public function getStates()
    {
        return array_keys($this->states);
    }

    /**
     * @param StatefulInterface $object
     */
    public function setObject(StatefulInterface $object)
    {
        $this->object = $object;
    }

    /**
     * @{inheritDoc}
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @{inheritDoc}
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }
}
