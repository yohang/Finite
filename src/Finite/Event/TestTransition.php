<?php

namespace Finite\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TestTransition extends Event
{
    public const NAME = FiniteEvents::TEST_TRANSITION;
}
