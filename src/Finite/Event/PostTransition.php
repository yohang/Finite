<?php

namespace Finite\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PostTransition extends Event
{
    public const NAME = FiniteEvents::POST_TRANSITION;
}
