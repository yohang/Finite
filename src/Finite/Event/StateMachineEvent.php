<?php

namespace Finite\Event;

use Finite\ListenableStateMachine;
use Symfony\Component\EventDispatcher\Event;

/**
 * The event object which is thrown on state machine actions
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachineEvent extends Event
{
    /**
     * @var ListenableStateMachine
     */
    protected $stateMachine;

    /**
     * @param ListenableStateMachine $stateMachine
     */
    public function __construct(ListenableStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * @return ListenableStateMachine
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }
}
