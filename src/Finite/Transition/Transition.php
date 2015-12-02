<?php

namespace Finite\Transition;

use Finite\Exception\TransitionException;
use Finite\StateMachine\StateMachineInterface;
use Finite\State\StateInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The base Transition class.
 * Feel free to extend it to fit to your needs.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class Transition implements PropertiesAwareTransitionInterface
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
        try {
            return $this->propertiesOptionsResolver->resolve($properties);
        } catch (MissingOptionsException $e) {
            throw new TransitionException(
                'Testing or applying this transition need a parameter. Provide it or set it optional.',
                $e->getCode(),
                $e
            );
        } catch (UndefinedOptionsException $e) {
            throw new TransitionException(
                'You provided an unknown property to test() or apply(). Remove it or declare it in your graph.',
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has($property)
    {
        return array_key_exists($property, $this->getProperties());
    }

    /**
     * {@inheritDoc}
     */
    public function get($property, $default = null)
    {
        $properties = $this->getProperties();

        return $this->has($property) ? $properties[$property] : $default;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties()
    {
        $missingOptions = $this->propertiesOptionsResolver->getMissingOptions();

        if (0 === count($missingOptions)) {
            return $this->propertiesOptionsResolver->resolve(array());
        }

        $options = array_combine($missingOptions, array_fill(0, count($missingOptions), null));

        return array_diff_key(
            $this->propertiesOptionsResolver->resolve($options),
            array_combine($missingOptions, $missingOptions)
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
