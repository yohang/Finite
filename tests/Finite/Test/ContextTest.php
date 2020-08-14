<?php

namespace Finite\Test;

use Finite\Context;
use Finite\Factory\PimpleFactory;
use  Finite\StateMachine\StateMachine;
use Finite\State\State;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    protected $object;

    protected $accessor;

    public function setUp()
    {
        $this->accessor = $accessor = $this->createMock('Finite\State\Accessor\StateAccessorInterface');
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
        $sm = $this->object->getStateMachine($this->createMock('Finite\StatefulInterface'));

        $this->assertInstanceOf('Finite\StateMachine\StateMachine', $sm);
        $this->assertSame('s1', $sm->getCurrentState()->getName());
    }

    public function testGetState()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertSame('s1', $this->object->getState($this->createMock('Finite\StatefulInterface')));
    }

    public function testGetTransitions()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertEquals(array('t12'), $this->object->getTransitions($this->createMock('Finite\StatefulInterface')));
    }

    public function testGetTransitionObjects()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));

        $transitions = $this->object->getTransitions($this->createMock('Finite\StatefulInterface'), 'default', true);

        $this->assertCount(1, $transitions);
        $this->assertInstanceOf('Finite\Transition\TransitionInterface', $transitions[0]);
    }

    public function testGetProperties()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertEquals(
            array('foo' => true, 'bar' => false),
            $this->object->getProperties($this->createMock('Finite\StatefulInterface'))
        );
    }

    public function testHasProperty()
    {
        $this->accessor->expects($this->exactly(2))->method('getState')->will($this->returnValue('s1'));
        $this->assertTrue($this->object->hasProperty($this->createMock('Finite\StatefulInterface'), 'foo'));
        $this->assertFalse($this->object->hasProperty($this->createMock('Finite\StatefulInterface'), 'baz'));
    }

    public function testItRetrievesGoodStateMachine()
    {
        $object  = $this->createMock('Finite\StatefulInterface');
        $factory = $this->createMock('Finite\Factory\FactoryInterface');
        $sm      = $this->createMock('Finite\StateMachine\StateMachineInterface');

        $factory->expects($this->once())->method('get')->with($object, 'foo')->will($this->returnValue($sm));

        $context = new Context($factory);
        $this->assertSame($sm, $context->getStateMachine($object, 'foo'));
    }
}
