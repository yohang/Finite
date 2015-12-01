<?php

namespace Finite\Factory;

use Finite\Exception\FactoryException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A concrete implementation of State Machine Factory using the sf2 DIC.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class SymfonyDependencyInjectionFactory extends AbstractFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $key;

    /**
     * @param ContainerInterface $container
     * @param string             $key
     */
    public function __construct(ContainerInterface $container, $key)
    {
        $this->container = $container;
        $this->key = $key;

        if (!$container->has($key)) {
            throw new FactoryException(
                sprintf(
                    'You must define the "%s" service as your StateMachine definition',
                    $key
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createStateMachine()
    {
        return $this->container->get($this->key);
    }
}
