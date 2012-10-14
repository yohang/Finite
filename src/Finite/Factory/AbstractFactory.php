<?php

namespace Finite\Factory;

use Finite\StatefulInterface;
use Finite\StateMachine;

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
     * @{inheritDoc}
     */
    public function get(StatefulInterface $object)
    {
        $hash = spl_object_hash($object);
        if (!isset($this->stateMachines[$hash])) {
            $stateMachine = $this->createStateMachine();
            $stateMachine->setObject($object);
            $stateMachine->initialize();

            $this->stateMachines[$hash] = $stateMachine;
        }

        return $this->stateMachines[$hash];
    }

    /**
     * Creates an instance of StateMachine
     *
     * @return StateMachine
     */
    abstract protected function createStateMachine();
}
