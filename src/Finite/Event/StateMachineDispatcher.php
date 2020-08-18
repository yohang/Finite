<?php

namespace Finite\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This class is here to provide a compatibility layer between symfony
 * versions from 2.8 to ^5.0
 */
class StateMachineDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher;
    }

    public function dispatch($eventName, StateMachineEvent $event)
    {
        if (self::isUnder43()) {
            return $this->eventDispatcher->dispatch($eventName, $event);
        }

        return $this->eventDispatcher->dispatch($event, $eventName);
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        return $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }

    private static function isUnder43()
    {
        static $result = null;

        if (null === $result) {
            $reflectionMethod     = new \ReflectionMethod(EventDispatcherInterface::class, 'dispatch');
            $reflectionParameters = $reflectionMethod->getParameters();
            $result               = count($reflectionParameters) >= 2 && 'event' === $reflectionParameters[1]->getName();
        }

        return $result;
    }
}
