<?php

namespace Finite\Factory;

use Finite\Loader\LoaderInterface;
use Finite\StatefulInterface;
use  Finite\StateMachine\StateMachine;

/**
 * The abstract base class for state machine factories
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * @var array<StateMachine>
     */
    protected $stateMachines = array();

    /**
     * @var array<LoaderInterface>
     */
    protected $loaders = array();

    /**
     * @{inheritDoc}
     */
    public function get(StatefulInterface $object)
    {
        $hash = spl_object_hash($object);
        if (!isset($this->stateMachines[$hash])) {
            $stateMachine = $this->createStateMachine();
            if (null !== ($loader = $this->getLoader($object))) {
                $loader->load($stateMachine);
            }
            $stateMachine->setObject($object);
            $stateMachine->initialize();

            $this->stateMachines[$hash] = $stateMachine;
        }

        return $this->stateMachines[$hash];
    }

    /**
     * @param LoaderInterface $loader
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * @param StatefulInterface $object
     *
     * @return LoaderInterface|null
     */
    protected function getLoader(StatefulInterface $object)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($object)) {
                return $loader;
            }
        }

        return null;
    }

    /**
     * Creates an instance of StateMachine
     *
     * @return StateMachine
     */
    abstract protected function createStateMachine();
}
