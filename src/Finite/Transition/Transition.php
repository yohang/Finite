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
     * @var array
     */
    protected $properties;     

    /**
     * @param string       $name
     * @param string|array $initialStates
     * @param string       $state
     */
    public function __construct($name, $initialStates, $state, $guard = null, $properties = array())
    {
        if (null !== $guard && !is_callable($guard)) {
            throw new \InvalidArgumentException('Invalid callable guard argument passed to Transition::__construct().');
        }

        $this->name          = $name;
        $this->state         = $state;
        $this->initialStates = (array) $initialStates;
        $this->guard         = $guard;
        $this->properties    = $properties;
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
    /**
     * @{inheritDoc}
     */
    public function has($property)
    {
        return array_key_exists($property, $this->properties);
    }

    /**
     * @{inheritDoc}
     */
    public function get($property, $default = null)
    {
        return $this->has($property) ? $this->properties[$property] : $default;
    }

    /**
     * @{inheritDoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }  
}
