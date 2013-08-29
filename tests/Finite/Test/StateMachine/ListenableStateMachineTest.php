<?php

namespace Finite\Test\StateMachine;

use Finite\Event\FiniteEvents;
use Finite\Event\StateMachineEvent;
use Finite\Event\TransitionEvent;
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

    protected function setUp()
    {
        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();
        $this->object = new ListenableStateMachine();
        $this->object->setEventDispatcher($this->dispatcher);
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
            ->with('finite.pre_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with('finite.pre_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with('finite.post_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->dispatcher
            ->expects($this->at(4))
            ->method('dispatch')
            ->with('finite.post_transition.t23', $this->isInstanceOf('Finite\Event\TransitionEvent'));

        $this->initialize();
        $this->object->apply('t23');
    }

    public function getObject()
    {
        return $this->object;
    }
}
