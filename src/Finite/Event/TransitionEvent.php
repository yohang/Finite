<?php

namespace Finite\Event;

use Finite\ListenableStateMachine;
use Finite\Transition\TransitionInterface;
use Symfony\Component\EventDispatcher\Event;

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
}
