<?php

namespace Finite\Event;

abstract class TransitionEvent extends Event
{
    public function __construct(
        object $object,
        private readonly string $transitionName,
        ?string $stateClass = null,
    )
    {
        parent::__construct($object, $stateClass);
    }

    public function getTransitionName(): string
    {
        return $this->transitionName;
    }
}
