<?php

namespace Finite\State;

use Finite\Transition\TransitionInterface;

/**
 * The base State class.
 * Feel free to extend it to fit to your needs
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class State implements StateInterface
{
    /**
     * The state type
     *
     * @var int
     */
    protected $type;

    /**
     * The transition name
     *
     * @var array
     */
    protected $transitions;

    /**
     * The state name
     *
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $properties;

    public function __construct($name, $type = self::TYPE_NORMAL, array $transitions = array(), array $properties = array())
    {
        $this->name        = $name;
        $this->type        = $type;
        $this->transitions = $transitions;
        $this->properties  = $properties;
    }

    /**
     * @{inheritDoc}
     */
    public function isInitial()
    {
        return self::TYPE_INITIAL === $this->type;
    }

    /**
     * @{inheritDoc}
     */
    public function isFinal()
    {
        return self::TYPE_FINAL === $this->type;
    }

    /**
     * @{inheritDoc}
     */
    public function isNormal()
    {
        return self::TYPE_NORMAL === $this->type;
    }

    /**
     * @{inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $transition
     */
    public function addTransition($transition)
    {
        if ($transition instanceof TransitionInterface) {
            $transition = $transition->getName();
        }

        $this->transitions[] = $transition;
    }

    /**
     * @param array $transitions
     */
    public function setTransitions(array $transitions)
    {
        foreach ($transitions as $transition) {
            $this->addTransition($transition);
        }
    }

    /**
     * @{inheritDoc}
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * @{inheritDoc}
     *
     * @deprecated Deprecated since version 1.0.0-BETA2. Use {@link StateMachine::can($transition)} instead.
     */
    public function can($transition)
    {
        if ($transition instanceof TransitionInterface) {
            $transition = $transition->getName();
        }

        return in_array($transition, $this->transitions);
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
     * @{inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
