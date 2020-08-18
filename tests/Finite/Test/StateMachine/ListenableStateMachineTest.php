<?php

namespace Finite\Test\StateMachine;

use Finite\StateMachine\ListenableStateMachine;
use Finite\Test\StateMachineTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ListenableStateMachineTest extends StateMachineTestCase
{
    /**
     * @var ListenableStateMachine
     */
    protected $object;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    public function setUp()
    {
        parent::setUp();

        $this->dispatcher = $this->getMockBuilder('Finite\Event\StateMachineDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new ListenableStateMachine(null, $this->dispatcher, $this->accessor);
    }

    public function testInitialize()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with('finite.initialize', $this->isInstanceOf('Finite\Event\StateMachineEvent'));

        $this->initialize();
    }

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
    }

    public function testCan()
    {
        $this->dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with('finite.test_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with('finite.test_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->initialize();
        $this->assertFalse($this->object->can('t34'));
        $this->assertTrue($this->object->can('t23'));
    }

    public function testCanWithListener()
    {
        $this->dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with('finite.test_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with('finite.test_transition.t23', $this->callback(function($event) {
                $event->reject();
                return $event instanceof \Finite\Event\TransitionEvent;
            }));

        $this->initialize();
        $this->assertFalse($this->object->can('t34'));
        $this->assertFalse($this->object->can('t23'));
    }

    public function getObject()
    {
        return $this->object;
    }
}
