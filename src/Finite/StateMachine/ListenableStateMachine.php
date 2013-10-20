<?php

namespace Finite\StateMachine;

use Finite\Event\FiniteEvents;
use Finite\Event\StateMachineEvent;
use Finite\Event\TransitionEvent;
use Finite\Transition\TransitionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * An Event-aware State machine.
 *
 * Uses the Symfony EventDispatcher Component
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ListenableStateMachine extends StateMachine
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @{inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->dispatcher->dispatch(FiniteEvents::INITIALIZE, new StateMachineEvent($this));
    }

    /**
     * @{inheritDoc}
     */
    public function apply($transitionName)
    {
        $transition = $this->getTransition($transitionName);
        $event      = new TransitionEvent($transition, $this);

        $this->dispatcher->dispatch(FiniteEvents::PRE_TRANSITION, $event);
        $this->dispatcher->dispatch(FiniteEvents::PRE_TRANSITION.'.'.$transitionName, $event);

        $value = parent::apply($transitionName);

        $this->dispatcher->dispatch(FiniteEvents::POST_TRANSITION, $event);
        $this->dispatcher->dispatch(FiniteEvents::POST_TRANSITION.'.'.$transitionName, $event);

        return $value;
    }

    /**
     * @{inheritDoc}
     */
    public function can($transition)
    {
        $transition = $transition instanceof TransitionInterface ? $transition : $this->getTransition($transition);

        if (!parent::can($transition->getName())) {
            return false;
        }

        $event = new TransitionEvent($transition, $this);
        $this->dispatcher->dispatch(FiniteEvents::TEST_TRANSITION, $event);
        $this->dispatcher->dispatch(FiniteEvents::TEST_TRANSITION.'.'.$transition->getName(), $event);

        return !$event->isRejected();
    }
}
