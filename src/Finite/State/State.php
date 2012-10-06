<?php

namespace Finite\State;

use Finite\Transition\TransitionInterface;

/**
 * The base State class.
 * Feel free to extend it to fit to your needs
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
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
    public function getProperties()
    {
        return array();
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
}
