<?php

namespace Finite\StateMachine;

use Finite\StatefulInterface;
use Finite\State\StateInterface;
use Finite\Transition\TransitionInterface;

/**
 * The Finite State Machine base Interface
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface StateMachineInterface
{
    /**
     * Initialize the State Machine current state
     */
    public function initialize();

    /**
     * Apply a transition
     *
     * @param string $transitionName
     *
     * @return mixed
     */
    public function apply($transitionName);

    /**
     * Returns if the transition is applicable
     *
     * @param string|TransitionInterface $transition
     *
     * @return bool
     */
    public function can($transition);

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
     * Returns a transition by its name
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
     * Returns an array containing all the transitions names
     *
     * @return array<string>
     */
    public function getTransitions();

    /**
     * Returns an array containing all the states names
     *
     * @return array<string>
     */
    public function getStates();

    /**
     * @param StatefulInterface $object
     */
    public function setObject(StatefulInterface $object);

    /**
     * @return StatefulInterface
     */
    public function getObject();

    /**
     * @return StateInterface
     */
    public function getCurrentState();

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher();
}
