<?php

namespace Finite\Factory;

use Finite\StateMachine\StateMachineInterface;

/**
 * The base interface for Finite's State Machine Factory.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface FactoryInterface
{
    /**
     * Returns a StateMachine instance initialized on $object.
     *
     * @param object $object
     * @param string $graph
     *
     * @return StateMachineInterface
     */
    public function get($object, $graph = 'default');
}
