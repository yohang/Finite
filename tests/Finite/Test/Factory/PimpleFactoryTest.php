<?php

namespace Finite\Test\Factory;

use Finite\Factory\PimpleFactory;
use Finite\StateMachine\StateMachine;
use PHPUnit\Framework\TestCase;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\StatefulInterface;
use Finite\Loader\LoaderInterface;

class PimpleFactoryTest extends TestCase
{
    /**
     * @var PimpleFactory
     */
    protected $object;

    protected $accessor;

    public function setUp(): void
    {
        $this->accessor = $accessor = $this->getMockBuilder(StateAccessorInterface::class)->getMock();
        $container = new \Pimple(array(
            'state_machine' => function() use ($accessor) {
                $sm =  new StateMachine(null, null, $accessor);
                $sm->addTransition('t12', 's1', 's2');
                $sm->addTransition('t23', 's2', 's3');

                return $sm;
            }
        ));

        $this->object = new PimpleFactory($container, 'state_machine');
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

    public function testLoad()
    {
        $object = $this->getMockBuilder(StatefulInterface::class)->getMock();
        $this->accessor->expects($this->any())->method('getState')->will($this->returnValue('s1'));

        $loader1 = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader1->expects($this->at(0))->method('supports')->with($object, 'foo')->will($this->returnValue(false));
        $loader1->expects($this->at(1))->method('supports')->with($object, 'bar')->will($this->returnValue(true));
        $loader2 = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader2->expects($this->at(0))->method('supports')->with($object, 'foo')->will($this->returnValue(true));
        $loader2->expects($this->at(1))->method('load');

        $this->object->addLoader($loader1);
        $this->object->addLoader($loader2);

        $this->object->get($object, 'foo');
        $this->object->get($object, 'bar');
    }
}
