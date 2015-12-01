<?php

namespace Finite\Loader;

use Finite\StateMachine\StateMachineInterface;

/**
 * State & Transitions Loader interface.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface LoaderInterface
{
    /**
     * Loads a state machine.
     *
     * @param StateMachineInterface $stateMachine
     */
    public function load(StateMachineInterface $stateMachine);

    /**
     * Returns if this loader supports $object for $graph.
     *
     * @param object $object
     * @param string $graph
     *
     * @return bool
     */
    public function supports($object, $graph = 'default');
}
