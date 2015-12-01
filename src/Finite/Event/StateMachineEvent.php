<?php

namespace Finite\Event;

use Finite\StateMachine\StateMachine;
use Symfony\Component\EventDispatcher\Event;

/**
 * The event object which is thrown on state machine actions.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachineEvent extends Event
{
    /**
     * @var StateMachine
     */
    protected $stateMachine;

    /**
     * @param StateMachine $stateMachine
     */
    public function __construct(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * @return StateMachine
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }
}
