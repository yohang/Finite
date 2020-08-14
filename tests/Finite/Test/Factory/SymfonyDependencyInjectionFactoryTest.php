<?php

namespace Finite\Test\Factory;

use Finite\Factory\SymfonyDependencyInjectionFactory;
use  Finite\StateMachine\StateMachine;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SymfonyDependencyInjectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PimpleFactory
     */
    protected $object;

    protected $accessor;

    public function setUp()
    {
        $this->accessor = $this->createMock('Finite\State\Accessor\StateAccessorInterface');
        $container = new ContainerBuilder;
        $definition = $container
            ->register('state_machine', 'Finite\StateMachine\StateMachine')
            ->setArguments(array(null, null, $this->accessor))
            ->addMethodCall('addTransition', array('t12', 's1', 's2'))
            ->addMethodCall('addTransition', array('t23', 's2', 's3'));

        if (method_exists($definition, 'setShared')) {
            $definition->setShared(false);
        } else {
            $definition->setScope('prototype');
        }

        $this->object = new SymfonyDependencyInjectionFactory($container, 'state_machine');
    }

    public function testGet()
    {
        $object = $this->createMock('Finite\StatefulInterface');
        $this->accessor->expects($this->at(0))->method('getState')->will($this->returnValue('s2'));
        $sm = $this->object->get($object);

        $this->assertInstanceOf('Finite\StateMachine\StateMachine', $sm);
        $this->assertSame('s2', $sm->getCurrentState()->getName());

        $object2 = $this->createMock('Finite\StatefulInterface');
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
