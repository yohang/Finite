<?php

namespace Finite\State;

use Finite\Transition\TransitionInterface;

/**
 * The base State class.
 * Feel free to extend it to fit to your needs.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class State implements StateInterface
{
    /**
     * The state type.
     *
     * @var int
     */
    protected $type;

    /**
     * The transition name.
     *
     * @var array
     */
    protected $transitions;

    /**
     * Callbacks of the state
     *
     * @var array
     */
    protected $callbacks;

    /**
     * The state name.
     *
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $properties;

    /**
     * State constructor.
     *
     * @param $name
     * @param string $type
     * @param array $transitions
     * @param array $properties
     * @param array $callbacks
     */
    public function __construct(
        $name,
        $type = self::TYPE_NORMAL,
        array $transitions = [],
        array $properties = [],
        array $callbacks = []
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->transitions = $transitions;
        $this->properties = $properties;
        $this->callbacks = $callbacks;
    }

    /**
     * {@inheritdoc}
     */
    public function isInitial()
    {
        return self::TYPE_INITIAL === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function isFinal()
    {
        return self::TYPE_FINAL === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function isNormal()
    {
        return self::TYPE_NORMAL === $this->type;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function has($property)
    {
        return array_key_exists($property, $this->properties);
    }

    /**
     * {@inheritdoc}
     */
    public function get($property, $default = null)
    {
        return $this->has($property) ? $this->properties[$property] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
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
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
