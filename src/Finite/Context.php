<?php

namespace Finite;

use Finite\Factory\FactoryInterface;

/**
 * The Finite context.
 * It provides easy ways to deal with Stateful objects, and factory
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
     * @param StatefulInterface $object
     * @param string            $graph
     *
     * @return string
     */
    public function getState(StatefulInterface $object, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->getName();
    }

    /**
     * @param StatefulInterface $object
     * @param string            $graph
     *
     * @return array<string>
     */
    public function getTransitions(StatefulInterface $object, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->getTransitions();
    }

    /**
     * @param StatefulInterface $object
     * @param string            $graph
     *
     * @return array<string>
     */
    public function getProperties(StatefulInterface $object, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->getProperties();
    }

    /**
     * @param StatefulInterface $object
     * @param string            $property
     * @param string            $graph
     *
     * @return bool
     */
    public function hasProperty(StatefulInterface $object, $property, $graph = 'default')
    {
        return $this->getStateMachine($object, $graph)->getCurrentState()->has($property);
    }

    /**
     * @param StatefulInterface $object
     * @param string            $graph
     *
     * @return \Finite\StateMachine\StateMachine
     */
    public function getStateMachine(StatefulInterface $object, $graph = 'default')
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
