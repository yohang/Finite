<?php

namespace Finite\Event;

class EventDispatcher
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

    public function dispatch(Event $event): void
    {
        if (!isset($this->listeners[get_class($event)])) {
            return;
        }

        foreach ($this->listeners[get_class($event)] as $listener) {
            $listener($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        }
    }
}
