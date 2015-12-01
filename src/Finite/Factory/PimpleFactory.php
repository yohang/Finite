<?php

namespace Finite\Factory;

use Pimple;

/**
 * A concrete implementation of State Machine Factory using Pimple.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
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
