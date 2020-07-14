<?php

namespace Finite\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PreTransition extends Event
{
    public const NAME = FiniteEvents::PRE_TRANSITION;
}
