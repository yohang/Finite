<?php

namespace Finite\Transition;

use Finite\StateMachine\StateMachineInterface;
use Finite\State\StateInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The base Transition class.
 * Feel free to extend it to fit to your needs.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class Transition implements TransitionInterface, PropertiesAwareTransitionInterface
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
     * @var OptionsResolver
     */
    protected $propertiesOptionsResolver;

    /**
     * @param string          $name
     * @param string|array    $initialStates
     * @param string          $state
     * @param callable|null   $guard
     * @param OptionsResolver $propertiesOptionsResolver
     */
    public function __construct(
        $name,
        $initialStates,
        $state,
        $guard = null,
        OptionsResolver $propertiesOptionsResolver = null
    ) {
        if (null !== $guard && !is_callable($guard)) {
            throw new \InvalidArgumentException('Invalid callable guard argument passed to Transition::__construct().');
        }

        $this->name = $name;
        $this->state = $state;
        $this->initialStates = (array) $initialStates;
        $this->guard = $guard;
        $this->propertiesOptionsResolver = $propertiesOptionsResolver ?: new OptionsResolver();
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
     * {@inheritdoc}
     */
    public function getInitialStates()
    {
        return $this->initialStates;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function process(StateMachineInterface $stateMachine)
    {
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function resolveProperties(array $properties)
    {
        return $this->propertiesOptionsResolver->resolve($properties);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
