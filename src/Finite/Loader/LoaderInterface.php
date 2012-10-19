<?php

namespace Finite\Loader;

use Finite\StatefulInterface;
use  Finite\StateMachine\StateMachine;

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
     * @param \Finite\StateMachine $stateMachine
     */
    public function load(StateMachine $stateMachine);

    /**
     * Returns if this loader supports $object
     *
     * @param StatefulInterface $object
     *
     * @return boolean
     */
    public function supports(StatefulInterface $object);
}
