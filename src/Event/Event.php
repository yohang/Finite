<?php

namespace Finite\Event;

abstract class Event
{
    private bool $propagationStopped = false;

    public function __construct(
        private readonly object $object,
        private readonly ?string $stateClass = null,
    )
    {
    }

    public function getObject(): object
    {
        return $this->object;
    }

    public function getStateClass(): ?string
    {
        return $this->stateClass;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
