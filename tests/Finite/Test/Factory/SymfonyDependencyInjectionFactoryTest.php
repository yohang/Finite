<?php

namespace Finite\Test\Factory;

use Finite\Factory\SymfonyDependencyInjectionFactory;
use  Finite\StateMachine\StateMachine;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\StatefulInterface;

class SymfonyDependencyInjectionFactoryTest extends TestCase
{
    /**
     * @var PimpleFactory
     */
    protected $object;

    protected $accessor;

    public function setUp(): void
    {
        $this->accessor = $this->getMockBuilder(StateAccessorInterface::class)->getMock();
        $container = new ContainerBuilder;
        $container
            ->register('state_machine', StateMachine::class)
            ->setShared(false)
            ->setArguments(array(null, null, $this->accessor))
            ->addMethodCall('addTransition', array('t12', 's1', 's2'))
            ->addMethodCall('addTransition', array('t23', 's2', 's3'));

        $this->object = new SymfonyDependencyInjectionFactory($container, 'state_machine');
    }

    public function testGet()
    {
        $object = $this->getMockBuilder(StatefulInterface::class)->getMock();
        $this->accessor->expects($this->at(0))->method('getState')->will($this->returnValue('s2'));
        $sm = $this->object->get($object);

        $this->assertInstanceOf(StateMachine::class, $sm);
        $this->assertSame('s2', $sm->getCurrentState()->getName());

        $object2 = $this->getMockBuilder(StatefulInterface::class)->getMock();
        $this->accessor->expects($this->at(0))->method('getState')->will($this->returnValue('s2'));
        $sm2 = $this->object->get($object2);

        $this->assertNotSame($sm, $sm2);
    }

    /**
     * @expectedException \Finite\Exception\FactoryException
     */
    public function testNoService()
    {
        new SymfonyDependencyInjectionFactory(new ContainerBuilder, 'state_machine');
    }
}
