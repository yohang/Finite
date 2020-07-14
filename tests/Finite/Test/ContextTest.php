<?php

namespace Finite\Test;

use Finite\Context;
use Finite\Factory\PimpleFactory;
use  Finite\StateMachine\StateMachine;
use Finite\State\State;
use PHPUnit\Framework\TestCase;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\StatefulInterface;
use Finite\Transition\TransitionInterface;
use Finite\StateMachine\StateMachineInterface;
use Finite\Factory\FactoryInterface;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ContextTest extends TestCase
{
    /**
     * @var Context
     */
    protected $object;

    protected $accessor;

    public function setUp(): void
    {
        $this->accessor = $accessor = $this->getMockBuilder(StateAccessorInterface::class)->getMock();
        $container = new \Pimple(array(
            'state_machine' => function() use ($accessor) {
                $sm =  new StateMachine(null, null, $accessor);
                $sm->addState(new State('s1', State::TYPE_INITIAL, array(), array('foo' => true, 'bar' => false)));
                $sm->addTransition('t12', 's1', 's2');
                $sm->addTransition('t23', 's2', 's3');

                return $sm;
            }
        ));

        $this->object = new Context(new PimpleFactory($container, 'state_machine'));
    }

    public function testGetStateMachine()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));

        $mock = $this->getMockBuilder(StatefulInterface::class)->getMock();
        $sm = $this->object->getStateMachine($mock);

        $this->assertInstanceOf(StateMachine::class, $sm);
        $this->assertSame('s1', $sm->getCurrentState()->getName());
    }

    public function testGetState()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertSame('s1', $this->object->getState($this->getMockBuilder(StatefulInterface::class)->getMock()));
    }

    public function testGetTransitions()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertEquals(array('t12'), $this->object->getTransitions($this->getMockBuilder(StatefulInterface::class)->getMock()));
    }

    public function testGetTransitionObjects()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));

        $transitions = $this->object->getTransitions($this->getMockBuilder(StatefulInterface::class)->getMock(), 'default', true);

        $this->assertCount(1, $transitions);
        $this->assertInstanceOf(TransitionInterface::class, $transitions[0]);
    }

    public function testGetProperties()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertEquals(
            array('foo' => true, 'bar' => false),
            $this->object->getProperties($this->getMockBuilder(StatefulInterface::class)->getMock())
        );
    }

    public function testHasProperty()
    {
        $this->accessor->expects($this->exactly(2))->method('getState')->will($this->returnValue('s1'));
        $this->assertTrue($this->object->hasProperty($this->getMockBuilder(StatefulInterface::class)->getMock(), 'foo'));
        $this->assertFalse($this->object->hasProperty($this->getMockBuilder(StatefulInterface::class)->getMock(), 'baz'));
    }

    public function testItRetrievesGoodStateMachine()
    {
        $object  = $this->getMockBuilder(StatefulInterface::class)->getMock();
        $factory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $sm      = $this->getMockBuilder(StateMachineInterface::class)->getMock();

        $factory->expects($this->once())->method('get')->with($object, 'foo')->will($this->returnValue($sm));

        $context = new Context($factory);
        $this->assertSame($sm, $context->getStateMachine($object, 'foo'));
    }
}
