<?php

namespace Finite\Event;

use Finite\StateMachine\ListenableStateMachine;
use Finite\Transition\TransitionInterface;

/**
 * The event object which is thrown on transitions actions
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class TransitionEvent extends StateMachineEvent
{
    /**
     * @var TransitionInterface
     */
    protected $transition;

    /**
     * @var boolean
     */
    protected $transitionRejected = false;

    /**
     * @param TransitionInterface    $transition
     * @param ListenableStateMachine $stateMachine
     */
    public function __construct(TransitionInterface $transition, ListenableStateMachine $stateMachine)
    {
        $this->transition = $transition;
        parent::__construct($stateMachine);
    }

    /**
     * @return TransitionInterface
     */
    public function getTransition()
    {
        return $this->transition;
    }

    public function isRejected()
    {
        return $this->transitionRejected;
    }

    public function reject()
    {
        $this->transitionRejected = true;
    }
}
