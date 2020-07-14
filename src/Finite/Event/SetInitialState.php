<?php

namespace Finite\Event;

use Finite\StateMachine\StateMachine;
use Symfony\Contracts\EventDispatcher\Event;

class SetInitialState extends StateMachineEvent
{
    public const NAME = FiniteEvents::SET_INITIAL_STATE;
}
