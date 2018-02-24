<?php

namespace Finite;

use Finite\Factory\FactoryInterface;

/**
 * The Finite context.
 * It provides easy ways to deal with Stateful objects, and factory.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class Context implements ContextInterface
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
     * {@inheritdoc}
     */
    public function getState($object, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->getName();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getProperties($object, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->getProperties();
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperty($object, $property, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->has($property);
    }

    /**
     * {@inheritdoc}
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
