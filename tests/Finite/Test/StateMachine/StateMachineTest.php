<?php

namespace Finite\Test\StateMachine;

use Finite\StateMachine\StateMachine;
use Finite\Test\StateMachineTestCase;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachineTest extends StateMachineTestCase
{
    public function testAddState()
    {
        $this->object->addState('foo');
        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getState('foo'));

        $stateMock = $this->getMock('Finite\State\StateInterface');
        $stateMock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $this->object->addState($stateMock);
        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getState('bar'));
    }

    public function testAddTransition()
    {
        $this->object->addTransition('t12', 'state1', 'state2');
        $this->assertInstanceOf('Finite\Transition\TransitionInterface', $this->object->getTransition('t12'));

        $transitionMock = $this->getMock('Finite\Transition\TransitionInterface');

        $transitionMock->expects($this->atLeastOnce())->method('getName')         ->will($this->returnValue('t23'));
        $transitionMock->expects($this->once())       ->method('getInitialStates')->will($this->returnValue(array('state2')));
        $transitionMock->expects($this->atLeastOnce())->method('getState')        ->will($this->returnValue('state3'));

        $this->object->addTransition($transitionMock);
        $this->assertInstanceOf('Finite\Transition\TransitionInterface', $this->object->getTransition('t23'));

        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getState('state3'));
    }

    public function testInitialize()
    {
        $this->initialize();
    }

    public function testGetCurrentState()
    {
        $this->initialize();
        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getCurrentState());
        $this->assertSame('s2', $this->object->getCurrentState()->getName());
    }

    public function testCan()
    {
        $this->initialize();
        $this->assertTrue($this->object->can('t23'));
        $this->assertFalse($this->object->can('t34'));
    }

    /**
     * @expectedException Finite\Exception\StateException
     */
    public function testApply()
    {
        $this->initialize();
        $this->object->apply('t23');
        $this->assertSame('s3', $this->object->getCurrentState()->getName());
        $this->object->apply('t23');
    }

    public function testGetStates()
    {
        $this->initialize();

        $this->assertSame(array('s1', 's2', 's3', 's4', 's5'), $this->object->getStates());
    }

    public function testGetTransitions()
    {
        $this->initialize();

        $this->assertSame(array('t12', 't23', 't34', 't45'), $this->object->getTransitions());
    }
}
