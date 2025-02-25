<?php

declare(strict_types=1);

namespace Finite\Event;

use Finite\State;
use Finite\Transition\TransitionInterface;

abstract class TransitionEvent extends Event
{
    public function __construct(
        object $object,
        private readonly TransitionInterface $transition,
        private readonly \BackedEnum&State $fromState,
    ) {
        parent::__construct($object, \get_class($this->fromState));
    }

    public function getTransition(): TransitionInterface
    {
        return $this->transition;
    }

    public function getFromState(): State&\BackedEnum
    {
        return $this->fromState;
    }
}
