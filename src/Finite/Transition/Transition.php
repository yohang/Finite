<?php

namespace Finite\Transition;

use Finite\StateMachine\StateMachine;
use Finite\State\StateInterface;

/**
 * The base Transition class.
 * Feel free to extend it to fit to your needs
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class Transition implements TransitionInterface
{
    /**
     * @var array
     */
    protected $initialStates;

    /*
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $guard;

    /**
     * @param string       $name
     * @param string|array $initialStates
     * @param string       $state
     */
    public function __construct($name, $initialStates, $state, $guard = null)
    {
        if (null !== $guard && !is_callable($guard)) {
            throw new \InvalidArgumentException('Invalid callable guard argument passed to Transition::__construct().');
        }

        $this->name          = $name;
        $this->state         = $state;
        $this->initialStates = (array) $initialStates;
        $this->guard = $guard;
    }

    /**
     * @param string|StateInterface $state
     */
    public function addInitialState($state)
    {
        if ($state instanceof StateInterface) {
            $state = $state->getName();
        }

        $this->initialStates[] = $state;
    }

    /**
     * @{inheritDoc}
     */
    public function getInitialStates()
    {
        return $this->initialStates;
    }

    /**
     * @{inheritDoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @{inheritDoc}
     */
    public function process(StateMachine $stateMachine)
    {
    }

    /**
     * @{inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return callable|null
     */
    public function getGuard()
    {
        return $this->guard;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
