<?php

namespace Finite\Factory;

use Finite\StateMachine\StateMachineInterface;
use Pimple;

/**
 * A concrete implementation of State Machine Factory using Pimple.
 *
 * @deprecated Pimple is not supported anymore (It was mostly here for silex)
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
class PimpleFactory extends AbstractFactory
{
    protected Pimple $container;

    protected string $id;

    public function __construct(Pimple $container, string $id)
    {
        $this->container = $container;
        $this->id        = $id;
    }

    /**
     * {@inheritdoc}
     */
    protected function createStateMachine(): StateMachineInterface
    {
        return $this->container[$this->id];
    }
}
