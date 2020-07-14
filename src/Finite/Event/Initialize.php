<?php

namespace Finite\Event;

use Symfony\Contracts\EventDispatcher\Event;

class Initialize extends StateMachineEvent
{
    public const NAME = FiniteEvents::INITIALIZE;
}
