<?php

declare(strict_types=1);

namespace Finite\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array<string,array<callable>>
     */
    private array $listeners = [];

    public function addEventListener(string $eventClass, callable $listener): void
    {
        if (!isset($this->listeners[$eventClass])) {
            $this->listeners[$eventClass] = [];
        }

        $this->listeners[$eventClass][] = $listener;
    }

    public function dispatch(object $event): void
    {
        if (!isset($this->listeners[$event::class])) {
            return;
        }

        foreach ($this->listeners[$event::class] as $listener) {
            $listener($event);

            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return;
            }
        }
    }
}
