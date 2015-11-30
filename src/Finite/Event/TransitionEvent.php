<?php

namespace Finite\Event;

use Finite\Exception\TransitionException;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;
use Finite\Transition\PropertiesAwareTransitionInterface;
use Finite\Transition\TransitionInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * The event object which is thrown on transitions actions
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
     * @var boolean
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
     *
     * @throws TransitionException
     */
    public function __construct(
        StateInterface $initialState,
        TransitionInterface $transition,
        StateMachine $stateMachine,
        array $properties = array()
    ) {
        $this->transition   = $transition;
        $this->initialState = $initialState;
        $this->properties   = $properties;

        if ($transition instanceof PropertiesAwareTransitionInterface) {
            try {
                $this->properties = $transition->resolveProperties($properties);
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
