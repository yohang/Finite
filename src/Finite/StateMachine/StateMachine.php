<?php

namespace Finite\StateMachine;

use Finite\Event\FiniteEvents;
use Finite\Event\StateMachineEvent;
use Finite\Event\TransitionEvent;
use Finite\Exception;
use Finite\State\Accessor\PropertyPathStateAccessor;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\StatefulInterface;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\Transition\Transition;
use Finite\Transition\TransitionInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
    protected $object;

    /**
     * The available states
     *
     * @var array
     */
    protected $states = array();

    /**
     * The available transitions
     *
     * @var array
     */
    protected $transitions = array();

    /**
     * The current state
     *
     * @var StateInterface
     */
    protected $currentState;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var StateAccessorInterface
     */
    protected $stateAccessor;

    /**
     * @param object                   $object
     * @param EventDispatcherInterface $dispatcher
     * @param StateAccessorInterface   $stateAccessor
     */
    public function __construct(
        $object = null,
        EventDispatcherInterface $dispatcher = null,
        StateAccessorInterface $stateAccessor = null
    )
    {
        $this->object        = $object;
        $this->dispatcher    = $dispatcher ?: new EventDispatcher;
        $this->stateAccessor = $stateAccessor ?: new PropertyPathStateAccessor;
    }

    /**
     * @{inheritDoc}
     */
    public function initialize()
    {
        $initialState = $this->stateAccessor->getState($this->object);
        if (null === $initialState) {
            $initialState = $this->findInitialState();
            $this->stateAccessor->setState($this->object, $initialState);

            $this->dispatcher->dispatch(FiniteEvents::SET_INITIAL_STATE, new StateMachineEvent($this));
        }

        $this->currentState = $this->getState($initialState);

        $this->dispatcher->dispatch(FiniteEvents::INITIALIZE, new StateMachineEvent($this));
    }

    /**
     * @{inheritDoc}
     *
     * @throws Exception\StateException
     */
    public function apply($transitionName)
    {
        $transition = $this->getTransition($transitionName);
        $event      = new TransitionEvent($this->getCurrentState(), $transition, $this);
        if (!$this->can($transition)) {
            throw new Exception\StateException(sprintf(
                'The "%s" transition can not be applied to the "%s" state.',
                $transition->getName(),
                $this->currentState->getName()
            ));
        }

        $this->dispatcher->dispatch(FiniteEvents::PRE_TRANSITION, $event);
        $this->dispatcher->dispatch(FiniteEvents::PRE_TRANSITION . '.' . $transitionName, $event);

        $returnValue = $transition->process($this);
        $this->stateAccessor->setState($this->object, $transition->getState());
        $this->currentState = $this->getState($transition->getState());

        $this->dispatcher->dispatch(FiniteEvents::POST_TRANSITION, $event);
        $this->dispatcher->dispatch(FiniteEvents::POST_TRANSITION . '.' . $transitionName, $event);

        return $returnValue;
    }

    /**
     * @{inheritDoc}
     */
    public function can($transition)
    {
        $transition = $transition instanceof TransitionInterface ? $transition : $this->getTransition($transition);

        if (null !== $transition->getGuard()) {
            return call_user_func($transition->getGuard());
        }

        if (!in_array($transition->getName(), $this->getCurrentState()->getTransitions())) {
            return false;
        }

        $event = new TransitionEvent($this->getCurrentState(), $transition, $this);
        $this->dispatcher->dispatch(FiniteEvents::TEST_TRANSITION, $event);
        $this->dispatcher->dispatch(FiniteEvents::TEST_TRANSITION . '.' . $transition->getName(), $event);

        return !$event->isRejected();
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
                'You must provide a TransitionInterface instance or the $transition, ' .
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
            throw new Exception\TransitionException('Unable to find a transition called ' . $name);
        }

        return $this->transitions[$name];
    }

    /**
     * @{inheritDoc}
     */
    public function getState($name)
    {
        if (!isset($this->states[$name])) {
            throw new Exception\StateException('Unable to find a state called ' . $name);
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

    /**
     * Find and return the Initial state if exists
     *
     * @return string
     *
     * @throws Exception\StateException
     */
    protected function findInitialState()
    {
        foreach ($this->states as $state) {
            if (State::TYPE_INITIAL === $state->getType()) {
                return $state->getName();
            }
        }

        throw new Exception\StateException('No initial state found.');
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param StateAccessorInterface $stateAccessor
     */
    public function setStateAccessor(StateAccessorInterface $stateAccessor)
    {
        $this->stateAccessor = $stateAccessor;
    }
}
