<?php

namespace Finite\Test\StateMachine;

use Finite\Event\StateMachineEvent;
use Finite\Event\TransitionEvent;
use Finite\Exception\StateException;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\StatefulInterface;
use Finite\Test\StateMachineTestCase;
use Finite\Transition\TransitionInterface;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachineTest extends StateMachineTestCase
{
    /**
     * @throws \Finite\Exception\TransitionException
     */
    public function testAddState(): void
    {
        $this->object->addState('foo');
        $this->assertInstanceOf(StateInterface::class, $this->object->getState('foo'));

        $stateMock = $this->createMock(StateInterface::class);
        $stateMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn('bar')
        ;

        $this->object->addState($stateMock);
        $this->assertInstanceOf(StateInterface::class, $this->object->getState('bar'));
    }

    /**
     * @throws \Finite\Exception\TransitionException
     */
    public function testAddTransition(): void
    {
        $this->object->addTransition('t12', 'state1', 'state2');
        $this->assertInstanceOf(TransitionInterface::class, $this->object->getTransition('t12'));

        $transitionMock = $this->createMock(TransitionInterface::class);

        $transitionMock->expects($this->atLeastOnce())->method('getName')->willReturn('t23');
        $transitionMock->expects($this->once())->method('getInitialStates')->willReturn(['state2']);
        $transitionMock->expects($this->atLeastOnce())->method('getState')->willReturn('state3');

        $this->object->addTransition($transitionMock);
        $this->assertInstanceOf(TransitionInterface::class, $this->object->getTransition('t23'));

        $this->assertInstanceOf(StateInterface::class, $this->object->getState('state3'));
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testInitialize(): void
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(...['finite.initialize', $this->isInstanceOf(StateMachineEvent::class)])
        ;

        $this->initialize();
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testInitializeWithInitialState(): void
    {
        $object = $this->createMock(StatefulInterface::class);

        $this->accessor->expects($this->at(1))->method('setState')->willReturn('s1');

        $this->addStates();
        $this->addTransitions();
        $this->object->setObject($object);
        $this->object->initialize();
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testGetCurrentState(): void
    {
        $this->initialize();
        $this->assertInstanceOf(StateInterface::class, $this->object->getCurrentState());
        $this->assertSame('s2', $this->object->getCurrentState()->getName());
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testCan(): void
    {
        $this->initialize();
        $this->assertTrue($this->object->can('t23'));
        $this->assertFalse($this->object->can('t34'));
    }

    public function testCanWithGuardReturningFalse(): void
    {
        $transition = $this->createMock(TransitionInterface::class);
        $transition->expects($this->any())
            ->method('getGuard')
            ->willReturn(
                static function () {
                    return false;
                }
            )
        ;

        $transition->expects($this->atLeastOnce())->method('getName')->willReturn('t');
        $transition->expects($this->once())->method('getInitialStates')->willReturn(['state1']);
        $this->object->addTransition($transition);

        $this->assertFalse($this->object->can($transition));
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testCanWithGuardReturningTrue(): void
    {
        $transition = $this->createMock(TransitionInterface::class);
        $transition->expects($this->any())
            ->method('getGuard')
            ->willReturn(
                static function () {
                    return true;
                }
            )
        ;

        $stateful = $this->createMock(StatefulInterface::class);
        $this->object->addState(new State('state1', State::TYPE_INITIAL));

        $this->object->setObject($stateful);
        $this->object->initialize();
        $transition->expects($this->atLeastOnce())->method('getName')->willReturn('t');
        $transition->expects($this->once())->method('getInitialStates')->willReturn(['state1']);
        $this->object->addTransition($transition);

        $this->assertTrue($this->object->can($transition));
    }

    /**
     * @throws \Finite\Exception\ObjectException
     * @throws \Finite\Exception\StateException
     */
    public function testApply(): void
    {
        $this->expectException(StateException::class);

        $this->dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(...['finite.test_transition', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(...['finite.test_transition.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(...['finite.pre_transition', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(4))
            ->method('dispatch')
            ->with(...['finite.pre_transition.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(5))
            ->method('dispatch')
            ->with(...['finite.post_transition', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(6))
            ->method('dispatch')
            ->with(...['finite.post_transition.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->initialize();
        $this->object->apply('t23');
        $this->assertSame('s3', $this->object->getCurrentState()->getName());
        $this->object->apply('t23');
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testGetStates(): void
    {
        $this->initialize();

        $this->assertSame(['s1', 's2', 's3', 's4', 's5'], $this->object->getStates());
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testGetTransitions(): void
    {
        $this->initialize();

        $this->assertSame(['t12', 't23', 't34', 't45'], $this->object->getTransitions());
    }

    /**
     * @throws \Finite\Exception\ObjectException
     * @throws \Finite\Exception\TransitionException
     */
    public function testGetStateFromObject(): void
    {
        $this->initialize();

        $state = $this->getMockBuilder('stdClass')
            ->setMethods(['__toString'])
            ->getMock()
        ;
        $state->expects($this->once())->method('__toString')->willReturn('s1');

        $this->assertInstanceOf(State::class, $this->object->getState($state));
    }

    /**
     * Test events with a named statemachine
     *
     * @throws \Finite\Exception\ObjectException
     * @throws \Finite\Exception\StateException
     */
    public function testApplyWithGraph(): void
    {
        $this->dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(...['finite.test_transition', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(...['finite.test_transition.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(...['finite.test_transition.foo.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(4))
            ->method('dispatch')
            ->with(...['finite.pre_transition', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(5))
            ->method('dispatch')
            ->with(...['finite.pre_transition.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(6))
            ->method('dispatch')
            ->with(...['finite.pre_transition.foo.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(7))
            ->method('dispatch')
            ->with(...['finite.post_transition', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(8))
            ->method('dispatch')
            ->with(...['finite.post_transition.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->dispatcher
            ->expects($this->at(9))
            ->method('dispatch')
            ->with(...['finite.post_transition.foo.t23', $this->isInstanceOf(TransitionEvent::class)])
        ;

        $this->object->setGraph('foo');

        $this->initialize();
        $this->object->apply('t23');
        $this->assertSame('s3', $this->object->getCurrentState()->getName());
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testItFindsStatesByPropertyName(): void
    {
        $this->initialize();
        $this->assertSame(['s2', 's4', 's5'], $this->object->findStateWithProperty('visible'));
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function testItFindsStatesByPropertyValue(): void
    {
        $this->initialize();
        $this->assertSame(['s2', 's4'], $this->object->findStateWithProperty('visible', true));
    }
}
