<?php

namespace Finite\Loader;

use Finite\StateMachine\StateMachineInterface;

/**
 * State & Transitions Loader interface.
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
interface LoaderInterface
{
    /**
     * Loads a state machine.
     */
    public function load(StateMachineInterface $stateMachine);

    /**
     * Returns if this loader supports $object for $graph.
     */
    public function supports(object $object, string $graph = 'default'): bool;

    /**
     * Returns if this loader supports the current object (any graph).
     */
    public function supportsObject(object $object): bool;

    public function getClassName(): string;

    public function getGraphName(): string;
}
