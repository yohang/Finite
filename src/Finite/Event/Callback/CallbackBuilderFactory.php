<?php

namespace Finite\Event\Callback;

use Finite\StateMachine\StateMachineInterface;

/**
 * Concrete implementation of CallbackBuilder factory.
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
class CallbackBuilderFactory implements CallbackBuilderFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createBuilder(StateMachineInterface $stateMachine)
    {
        return CallbackBuilder::create($stateMachine);
    }
}
