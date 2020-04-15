<?php

namespace Finite\Test\Factory;

use Finite\Exception\FactoryException;
use Finite\Factory\SymfonyDependencyInjectionFactory;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SymfonyDependencyInjectionFactoryTest extends TestCase
{
    /**
     * @var \Finite\Factory\PimpleFactory
     */
    protected $object;

    protected $accessor;

    /**
     * @throws \Finite\Exception\FactoryException
     */
    public function setUp(): void
    {
        $this->accessor = $this->createMock(StateAccessorInterface::class);
        $container = new ContainerBuilder;
        $container
            ->register('state_machine', StateMachine::class)
            ->setShared(false)
            ->setArguments([null, null, $this->accessor])
            ->addMethodCall('addTransition', ['t12', 's1', 's2'])
            ->addMethodCall('addTransition', ['t23', 's2', 's3'])
        ;

        $this->object = new SymfonyDependencyInjectionFactory($container, 'state_machine');
    }

    public function testGet(): void
    {
        $object = $this->createMock(StatefulInterface::class);

        $this->accessor->expects($this->at(0))
            ->method('getState')
            ->willReturn('s2')
        ;

        $sm = $this->object->get($object);

        $this->assertInstanceOf(StateMachine::class, $sm);
        $this->assertSame('s2', $sm->getCurrentState()->getName());

        $object2 = $this->createMock(StatefulInterface::class);
        $this->accessor->expects($this->at(0))->method('getState')->willReturn('s2');
        $sm2 = $this->object->get($object2);

        $this->assertNotSame($sm, $sm2);
    }

    /**
     * @throws \Finite\Exception\FactoryException
     */
    public function testNoService(): void
    {
        $this->expectException(FactoryException::class);

        new SymfonyDependencyInjectionFactory(new ContainerBuilder, 'state_machine');
    }
}
