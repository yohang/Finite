<?php

namespace Finite\StateMachine;

use Finite\Event\StateMachineDispatcher;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\State\StateInterface;
use Finite\Transition\TransitionInterface;

/**
 * The Finite State Machine base Interface.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface StateMachineInterface
{
    /**
     * Initialize the State Machine current state.
     */
    public function initialize();

    /**
     * Apply a transition.
     *
     * @param string $transitionName
     * @param array  $parameters
     *
     * @return mixed
     */
    public function apply($transitionName, array $parameters = array());

    /**
     * Returns if the transition is applicable.
     *
     * @param string|TransitionInterface $transition
     * @param array                      $parameters
     *
     * @return bool
     */
    public function can($transition, array $parameters = array());

    /**
     * @param string|StateInterface $state
     */
    public function addState($state);

    /**
     * @param string|TransitionInterface $transition
     * @param string|null                $initialState
     * @param string|null                $finalState
     *
     * @throws \InvalidArgumentException
     */
    public function addTransition($transition, $initialState = null, $finalState = null);

    /**
     * Returns a transition by its name.
     *
     * @param string $name
     *
     * @return TransitionInterface
     *
     * @throws \Finite\Exception\TransitionException
     */
    public function getTransition($name);

    /**
     * @param string $name
     *
     * @return StateInterface
     *
     * @throws \Finite\Exception\TransitionException
     */
    public function getState($name);

    /**
     * Returns an array containing all the transitions names.
     *
     * @return array<string>
     */
    public function getTransitions();

    /**
     * Returns an array containing all the states names.
     *
     * @return array<string>
     */
    public function getStates();

    /**
     * @param object $object
     */
    public function setObject($object);

    /**
     * @return object
     */
    public function getObject();

    /**
     * @return StateInterface
     */
    public function getCurrentState();

    /**
     * @return StateMachineDispatcher
     */
    public function getDispatcher();

    /**
     * @param StateAccessorInterface $stateAccessor
     */
    public function setStateAccessor(StateAccessorInterface $stateAccessor);

    /**
     * @return bool
     */
    public function hasStateAccessor();

    /**
     * @param string $graph
     */
    public function setGraph($graph);

    /**
     * @return string
     */
    public function getGraph();

    /**
     * Find a state which have a given property, with an optional given value.
     * It is useful for looking for objects having a given property in database for example.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return bool
     */
    public function findStateWithProperty($property, $value = null);
}
