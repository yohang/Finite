<?php

namespace Finite\Event;

use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;
use Finite\Transition\PropertiesAwareTransitionInterface;
use Finite\Transition\TransitionInterface;

/**
 * The event object which is thrown on transitions actions.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class TransitionEvent extends StateMachineEvent
{
    /**
     * @var TransitionInterface
     */
    protected $transition;

    /**
     * @var bool
     */
    protected $transitionRejected = false;

    /**
     * @var StateInterface
     */
    protected $initialState;

    /**
     * @var array
     */
    protected $properties;

    /**
     * @param StateInterface      $initialState
     * @param TransitionInterface $transition
     * @param StateMachine        $stateMachine
     * @param array               $properties
     */
    public function __construct(
        StateInterface $initialState,
        TransitionInterface $transition,
        StateMachine $stateMachine,
        array $properties = array()
    ) {
        $this->transition = $transition;
        $this->initialState = $initialState;
        $this->properties = $properties;

        if ($transition instanceof PropertiesAwareTransitionInterface) {
            $this->properties = $transition->resolveProperties($properties);
        }

        parent::__construct($stateMachine);
    }

    /**
     * @return TransitionInterface
     */
    public function getTransition()
    {
        return $this->transition;
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        return $this->transitionRejected;
    }

    public function reject()
    {
        $this->transitionRejected = true;
    }

    /**
     * @return StateInterface
     */
    public function getInitialState()
    {
        return $this->initialState;
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    public function has($property)
    {
        return array_key_exists($property, $this->properties);
    }

    /**
     * @param string $property
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($property, $default = null)
    {
        return $this->has($property) ? $this->properties[$property] : $default;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
