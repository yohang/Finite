<?php

namespace Finite\Transition;

use Finite\StateMachine\StateMachineInterface;

/**
 * The base Transition interface.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface TransitionInterface
{
    /**
     * Returns the array of states that supports this transition.
     *
     * @return array
     */
    public function getInitialStates();

    /**
     * Returns the state resulting of this transition.
     *
     * @return string
     */
    public function getState();

    /**
     * Process the transition.
     *
     * @param StateMachineInterface $stateMachine
     *
     * @return mixed
     */
    public function process(StateMachineInterface $stateMachine);

    /**
     * Returns the name of the transition.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the closure. If closure execution returns false, transition cannot be applied.
     *
     * @return callable
     */
    public function getGuard();
}
