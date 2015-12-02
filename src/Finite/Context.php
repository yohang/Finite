<?php

namespace Finite;

use Finite\Factory\FactoryInterface;
use Finite\StateMachine\StateMachine;

/**
 * The Finite context.
 * It provides easy ways to deal with Stateful objects, and factory.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class Context
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param object $object
     * @param string $graph
     *
     * @return string
     */
    public function getState($object, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->getName();
    }

    /**
     * @param object $object
     * @param string $graph
     * @param bool   $asObject
     *
     * @return array<string>
     */
    public function getTransitions($object, $graph = 'default', $asObject = false)
    {
        if (!$asObject) {
            return $this->getStateMachine($object, $graph)->getCurrentState()->getTransitions();
        }

        $stateMachine = $this->getStateMachine($object, $graph);

        return array_map(
            function ($transition) use ($stateMachine) {
                return $stateMachine->getTransition($transition);
            },
            $stateMachine->getCurrentState()->getTransitions()
        );
    }

    /**
     * @param object $object
     * @param string $graph
     *
     * @return array<string>
     */
    public function getProperties($object, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->getProperties();
    }

    /**
     * @param object $object
     * @param string $property
     * @param string $graph
     *
     * @return bool
     */
    public function hasProperty($object, $property, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->has($property);
    }

    /**
     * @param object $object
     * @param string $graph
     *
     * @return StateMachine
     */
    public function getStateMachine($object, $graph = 'default')
    {
        return $this->getFactory()->get($object, $graph);
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
