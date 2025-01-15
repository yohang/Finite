<?php

declare(strict_types=1);

namespace Finite\Event;

use Psr\EventDispatcher\StoppableEventInterface;

abstract class Event implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function __construct(
        private readonly object $object,
        private readonly ?string $stateClass = null,
    ) {
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
