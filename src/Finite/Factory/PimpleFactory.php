<?php

namespace Finite\Factory;

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
    /**
     * @var Pimple
     */
    protected $container;

    /**
     * @var string
     */
    protected $id;

    /**
     * @param Pimple $container
     * @param string $id
     */
    public function __construct(Pimple $container, $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    protected function createStateMachine()
    {
        return $this->container[$this->id];
    }
}
