<?php

namespace Finite\Event\Callback;

use Finite\StateMachine\StateMachineInterface;

/**
 * Concrete implementation of CallbackBuilder factory.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
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
