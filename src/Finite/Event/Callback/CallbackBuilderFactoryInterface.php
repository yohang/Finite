<?php

namespace Finite\Event\Callback;

use Finite\StateMachine\StateMachineInterface;

/**
 * Base interface for CallbackBuilder factories.
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
interface CallbackBuilderFactoryInterface
{
    /**
     * @param StateMachineInterface $stateMachine
     *
     * @return mixed
     */
    public function createBuilder(StateMachineInterface $stateMachine);
}
