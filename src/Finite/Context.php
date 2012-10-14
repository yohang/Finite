<?php

namespace Finite;

use Finite\Factory\FactoryInterface;
use Finite\Transition\TransitionInterface;

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
     *
     * @return string
     */
    public function getState(StatefulInterface $object)
    {
        return $this->getStateMachine($object)->getCurrentState()->getName();
    }

    /**
     * @param StatefulInterface $object
     *
     * @return array<string>
     */
    public function getTransitions(StatefulInterface $object)
    {
        return $this->getStateMachine($object)->getCurrentState()->getTransitions();
    }

    /**
     * @param StatefulInterface $object
     *
     * @return array<string>
     */
    public function getProperties(StatefulInterface $object)
    {
        return $this->getStateMachine($object)->getCurrentState()->getProperties();
    }

    /**
     * @param StatefulInterface $object
     * @param string            $property
     *
     * @return bool
     */
    public function hasProperty(StatefulInterface $object, $property)
    {
        return $this->getStateMachine($object)->getCurrentState()->has($property);
    }

    /**
     * @param StatefulInterface $object
     *
     * @return StateMachine
     */
    public function getStateMachine(StatefulInterface $object)
    {
        return $this->getFactory()->get($object);
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
