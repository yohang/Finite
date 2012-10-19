<?php

namespace Finite\Loader;

use Finite\StatefulInterface;
use Finite\StateMachine\StateMachineInterface;

/**
 * State & Transitions Loader interface
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface LoaderInterface
{
    /**
     * Loads a state machine
     *
     * @param StateMachineInterface $stateMachine
     */
    public function load(StateMachineInterface $stateMachine);

    /**
     * Returns if this loader supports $object
     *
     * @param StatefulInterface $object
     *
     * @return boolean
     */
    public function supports(StatefulInterface $object);
}
