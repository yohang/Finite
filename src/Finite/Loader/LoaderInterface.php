<?php

namespace Finite\Loader;

use Finite\StateMachine;

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
}
