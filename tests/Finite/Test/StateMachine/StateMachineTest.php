<?php

namespace Finite\Test\StateMachine;

use Finite\State\State;
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

        $stateMock = $this->createMock('Finite\State\StateInterface');
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

        $transitionMock = $this->createMock('Finite\Transition\TransitionInterface');

        $transitionMock->expects($this->atLeastOnce())->method('getName')         ->will($this->returnValue('t23'));
        $transitionMock->expects($this->once())       ->method('getInitialStates')->will($this->returnValue(array('state2')));
        $transitionMock->expects($this->atLeastOnce())->method('getState')        ->will($this->returnValue('state3'));

        $this->object->addTransition($transitionMock);
        $this->assertInstanceOf('Finite\Transition\TransitionInterface', $this->object->getTransition('t23'));

        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getState('state3'));
    }

    public function testInitialize()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('finite.initialize', $this->isInstanceOf('Finite\Event\StateMachineEvent'));

        $this->initialize();
    }

    public function testInitializeWithInitialState()
    {
        $object = $this->createMock('Finite\StatefulInterface');

        $this->accessor->expects($this->at(1))->method('setState')->will($this->returnValue('s1'));

        $this->addStates();
        $this->addTransitions();
        $this->object->setObject($object);
        $this->object->initialize();
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

    public function testCanWithGuardReturningFalse()
    {
        $transition = $this->createMock('\Finite\Transition\TransitionInterface');
        $transition->expects($this->any())
            ->method('getGuard')
            ->will($this->returnValue(function () {
                return false;
            }));

        $transition->expects($this->atLeastOnce())->method('getName')         ->will($this->returnValue('t'));
        $transition->expects($this->once())       ->method('getInitialStates')->will($this->returnValue(array('state1')));
        $this->object->addTransition($transition);
        $this->assertFalse($this->object->can($transition));
    }

    public function testCanWithGuardReturningTrue()
    {
        $transition = $this->createMock('\Finite\Transition\TransitionInterface');
        $transition->expects($this->any())
            ->method('getGuard')
            ->will($this->returnValue(function () {
                return true;
            }));

        $stateful = $this->createMock('Finite\StatefulInterface');
        $this->object->addState(new State('state1', State::TYPE_INITIAL));

        $this->object->setObject($stateful);
        $this->object->initialize();
        $transition->expects($this->atLeastOnce())->method('getName')         ->will($this->returnValue('t'));
        $transition->expects($this->once())       ->method('getInitialStates')->will($this->returnValue(array('state1')));
        $this->object->addTransition($transition);

        $this->assertTrue($this->object->can($transition));
    }

    /**
     * @expectedException \Finite\Exception\StateException
     */
    public function testApply()
    {
        $this->dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with('finite.test_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with('finite.test_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with('finite.pre_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(4))
            ->method('dispatch')
            ->with('finite.pre_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(5))
            ->method('dispatch')
            ->with('finite.post_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(6))
            ->method('dispatch')
            ->with('finite.post_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

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

    public function testGetStateFromObject()
    {
        $this->initialize();

        $state = $this->createMock(Stringable::class, array('__toString'));
        $state->expects($this->once())->method('__toString')->will($this->returnValue('s1'));

        $this->assertInstanceOf('Finite\State\State', $this->object->getState($state));
    }

    /**
     * Test events with a named statemachine
     */
    public function testApplyWithGraph()
    {

        $this->dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with('finite.test_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with('finite.test_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with('finite.test_transition.foo.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(4))
            ->method('dispatch')
            ->with('finite.pre_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(5))
            ->method('dispatch')
            ->with('finite.pre_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(6))
            ->method('dispatch')
            ->with('finite.pre_transition.foo.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(7))
            ->method('dispatch')
            ->with('finite.post_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(8))
            ->method('dispatch')
            ->with('finite.post_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(9))
            ->method('dispatch')
            ->with('finite.post_transition.foo.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->object->setGraph('foo');

        $this->initialize();
        $this->object->apply('t23');
        $this->assertSame('s3', $this->object->getCurrentState()->getName());
    }

    public function testItFindsStatesByPropertyName()
    {
        $this->initialize();
        $this->assertSame(array('s2', 's4', 's5'), $this->object->findStateWithProperty('visible'));
    }

    public function testItFindsStatesByPropertyValue()
    {
        $this->initialize();
        $this->assertSame(array('s2', 's4'), $this->object->findStateWithProperty('visible', true));
    }
}

interface Stringable
{
    public function __toString();
}
