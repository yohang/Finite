<?php

namespace Finite\Test\Callback;
use Finite\Callback\CascadeTransitionCallback;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CascadeTransitionCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CallbackHandler
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = $this->getMock('Finite\Factory\FactoryInterface');
        $this->object  = new CascadeTransitionCallback($this->factory);
    }

    public function testItAppliesTransition()
    {
        $stateMachine = $this->getMock('Finite\StateMachine\StateMachineInterface');
        $event        = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $object       = $this->getMock('Finite\StatefulInterface');

        $this->factory->expects($this->any())
            ->method('get')
            ->with($object, 'graph')
            ->will($this->returnValue($stateMachine))
        ;

        $stateMachine->expects($this->once())->method('apply')->with('transition');

        $this->object->apply($object, $event, 'transition', 'graph');
    }

    public function testItAppliesTransitionWithDefaultGraph()
    {
        $stateMachine = $this->getMock('Finite\StateMachine\StateMachineInterface');
        $event        = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $object       = $this->getMock('Finite\StatefulInterface');

        $this->factory->expects($this->any())
            ->method('get')
            ->with($object, 'graph')
            ->will($this->returnValue($stateMachine))
        ;

        $event->expects($this->once())->method('getStateMAchine')->will($this->returnValue($stateMachine));

        $stateMachine->expects($this->once())->method('apply')->with('transition');
        $stateMachine->expects($this->once())->method('getGraph')->will($this->returnValue('graph'));

        $this->object->apply($object, $event, 'transition');
    }

    public function testItAppliesTransitionWithDefaultGraphAndDefaultTransition()
    {
        $stateMachine = $this->getMock('Finite\StateMachine\StateMachineInterface');
        $event        = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $transition   = $this->getMock('Finite\Transition\TransitionInterface');
        $object       = $this->getMock('Finite\StatefulInterface');

        $this->factory->expects($this->any())
            ->method('get')
            ->with($object, 'graph')
            ->will($this->returnValue($stateMachine))
        ;

        $event->expects($this->once())->method('getStateMAchine')->will($this->returnValue($stateMachine));
        $event->expects($this->once())->method('getTransition')->will($this->returnValue($transition));

        $stateMachine->expects($this->once())->method('apply')->with('transition');
        $stateMachine->expects($this->once())->method('getGraph')->will($this->returnValue('graph'));

        $transition->expects($this->once())->method('getName')->will($this->returnValue('transition'));

        $this->object->apply($object, $event);
    }
}
