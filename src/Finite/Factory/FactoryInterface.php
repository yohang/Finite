<?php

namespace Finite\Factory;

use Finite\Exception\FactoryException;
use Finite\StateMachine\StateMachineInterface;

/**
 * The base interface for Finite's State Machine Factory.
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
interface FactoryInterface
{
    /**
     * Returns a StateMachine instance initialized on $object.
     *
     * @throws FactoryException
     */
    public function get(object $object, string $graph = 'default'): StateMachineInterface;

    /**
     * @param object $object
     *
     * @return iterable<int,StateMachineInterface>
     */
    public function getAllForObject(object $object): iterable;
}
