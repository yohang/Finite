<?php

namespace Finite\StateMachine;

use Finite\Event\FiniteEvents;
use Finite\Event\StateMachineDispatcher;
use Finite\Event\StateMachineEvent;
use Finite\Event\TransitionEvent;
use Finite\Exception;
use Finite\State\Accessor\PropertyPathStateAccessor;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\Transition\Transition;
use Finite\Transition\TransitionInterface;

/**
 * The Finite State Machine.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachine implements StateMachineInterface
{
    /**
     * The stateful object.
     *
     * @var object
     */
    protected $object;

    /**
     * The available states.
     *
     * @var array
     */
    protected $states = array();

    /**
     * The available transitions.
     *
     * @var array
     */
    protected $transitions = array();

    /**
     * The current state.
     *
     * @var StateInterface
     */
    protected $currentState;

    /**
     * @var StateMachineDispatcher
     */
    protected $dispatcher;

    /**
     * @var StateAccessorInterface
     */
    protected $stateAccessor;

    /**
     * @var string
     */
    protected $graph;

    /**
     * @param object                   $object
     * @param StateMachineDispatcher   $dispatcher
     * @param StateAccessorInterface   $stateAccessor
     */
    public function __construct(
        $object = null,
        StateMachineDispatcher $dispatcher = null,
        StateAccessorInterface $stateAccessor = null
    ) {
        $this->object = $object;
        $this->dispatcher = $dispatcher ?: new StateMachineDispatcher();
        $this->stateAccessor = $stateAccessor ?: new PropertyPathStateAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        if (null === $this->object) {
            throw new Exception\ObjectException('No object bound to the State Machine');
        }

        try {
            $initialState = $this->stateAccessor->getState($this->object);
        } catch (Exception\NoSuchPropertyException $e) {
            throw new Exception\ObjectException(sprintf(
               'StateMachine can\'t be initialized because the defined property_path of object "%s" does not exist.',
                $this->getObject() ? get_class($this->getObject()) : null
            ), $e->getCode(), $e);
        }

        if (null === $initialState) {
            $initialState = $this->findInitialState();
            $this->stateAccessor->setState($this->object, $initialState);

            $this->dispatcher->dispatch(FiniteEvents::SET_INITIAL_STATE, new StateMachineEvent($this));
        }

        $this->currentState = $this->getState($initialState);

        $this->dispatcher->dispatch(FiniteEvents::INITIALIZE, new StateMachineEvent($this));
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception\StateException
     */
    public function apply($transitionName, array $parameters = array())
    {
        $transition = $this->getTransition($transitionName);
        $event = new TransitionEvent($this->getCurrentState(), $transition, $this, $parameters);
        if (!$this->can($transition, $parameters)) {
            throw new Exception\StateException(sprintf(
                'The "%s" transition can not be applied to the "%s" state of object "%s" with graph "%s".',
                $transition->getName(),
                $this->currentState->getName(),
                $this->getObject() ? get_class($this->getObject()) : null,
                $this->getGraph()
            ));
        }

        $this->dispatchTransitionEvent($transition, $event, FiniteEvents::PRE_TRANSITION);

        $returnValue = $transition->process($this);
        $this->stateAccessor->setState($this->object, $transition->getState());
        $this->currentState = $this->getState($transition->getState());

        $this->dispatchTransitionEvent($transition, $event, FiniteEvents::POST_TRANSITION);

        return $returnValue;
    }

    /**
     * {@inheritdoc}
     */
    public function can($transition, array $parameters = array())
    {
        $transition = $transition instanceof TransitionInterface ? $transition : $this->getTransition($transition);

        if (null !== $transition->getGuard() && !call_user_func($transition->getGuard(), $this)) {
            return false;
        }

        if (!in_array($transition->getName(), $this->getCurrentState()->getTransitions())) {
            return false;
        }

        $event = new TransitionEvent($this->getCurrentState(), $transition, $this, $parameters);
        $this->dispatchTransitionEvent($transition, $event, FiniteEvents::TEST_TRANSITION);

        return !$event->isRejected();
    }

    /**
     * {@inheritdoc}
     */
    public function addState($state)
    {
        if (!$state instanceof StateInterface) {
            $state = new State($state);
        }

        $this->states[$state->getName()] = $state;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getTransition($name)
    {
        if (!isset($this->transitions[$name])) {
            throw new Exception\TransitionException(sprintf(
                'Unable to find a transition called "%s" on object "%s" with graph "%s".',
                $name,
                $this->getObject() ? get_class($this->getObject()) : null,
                $this->getGraph()
            ));
        }

        return $this->transitions[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getState($name)
    {
        $name = (string) $name;

        if (!isset($this->states[$name])) {
            throw new Exception\StateException(sprintf(
                'Unable to find a state called "%s" on object "%s" with graph "%s".',
                $name,
                $this->getObject() ? get_class($this->getObject()) : null,
                $this->getGraph()
            ));
        }

        return $this->states[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitions()
    {
        return array_keys($this->transitions);
    }

    /**
     * {@inheritdoc}
     */
    public function getStates()
    {
        return array_keys($this->states);
    }

    /**
     * {@inheritdoc}
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * Find and return the Initial state if exists.
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

        throw new Exception\StateException(sprintf(
            'No initial state found on object "%s" with graph "%s".',
            $this->getObject() ? get_class($this->getObject()) : null,
            $this->getGraph()
        ));
    }

    /**
     * @param StateMachineDispatcher $dispatcher
     */
    public function setDispatcher(StateMachineDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return StateMachineDispatcher
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

    /**
     * {@inheritdoc}
     */
    public function hasStateAccessor()
    {
        return null !== $this->stateAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function setGraph($graph)
    {
        $this->graph = $graph;
    }

    /**
     * {@inheritdoc}
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * {@inheritDoc}
     */
    public function findStateWithProperty($property, $value = null)
    {
        return array_keys(
            array_map(
                function (State $state) {
                    return $state->getName();
                },
                array_filter(
                    $this->states,
                    function (State $state) use ($property, $value) {
                        if (!$state->has($property)) {
                            return false;
                        }

                        if (null !== $value && $state->get($property) !== $value) {
                            return false;
                        }

                        return true;
                    }
                )
            )
        );
    }

    /**
     * Dispatches event for the transition
     *
     * @param TransitionInterface $transition
     * @param TransitionEvent $event
     * @param type $transitionState
     */
    private function dispatchTransitionEvent(TransitionInterface $transition, TransitionEvent $event, $transitionState)
    {
        $this->dispatcher->dispatch($transitionState, $event);
        $this->dispatcher->dispatch($transitionState.'.'.$transition->getName(), $event);
        if (null !== $this->getGraph()) {
            $this->dispatcher->dispatch($transitionState.'.'.$this->getGraph().'.'.$transition->getName(), $event);
        }
    }
}
