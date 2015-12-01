<?php

namespace Finite\Factory;

use Finite\Loader\LoaderInterface;
use Finite\StateMachine\StateMachineInterface;

/**
 * The abstract base class for state machine factories.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * @var StateMachineInterface[]
     */
    protected $stateMachines = array();

    /**
     * @var LoaderInterface[]
     */
    protected $loaders = array();

    /**
     * {@inheritdoc}
     */
    public function get($object, $graph = 'default')
    {
        $hash = spl_object_hash($object).'.'.$graph;
        if (!isset($this->stateMachines[$hash])) {
            $stateMachine = $this->createStateMachine();
            if (null !== ($loader = $this->getLoader($object, $graph))) {
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
     * @param object $object
     * @param string $graph
     *
     * @return LoaderInterface|null
     */
    protected function getLoader($object, $graph)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($object, $graph)) {
                return $loader;
            }
        }

        return;
    }

    /**
     * Creates an instance of StateMachine.
     *
     * @return StateMachineInterface
     */
    abstract protected function createStateMachine();
}
