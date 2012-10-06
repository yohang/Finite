<?php

namespace Finite\Test;

use Finite\Event\FiniteEvents;
use Finite\Event\StateMachineEvent;
use Finite\Event\TransitionEvent;
use Finite\ListenableStateMachine;
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
        $this->object = new ListenableStateMachine();
        $this->object->setEventDispatcher($this->dispatcher = new EventDispatcher);
    }

    public function testInitialize()
    {
        $initialized = false;
        $that        = $this;
        $this->dispatcher->addListener(
            FiniteEvents::INITIALIZE,
            function(StateMachineEvent $event) use(&$initialized, $that) {
                $initialized = true;
                $that->assertSame($that->object, $event->getStateMachine());
            }
        );
        $this->initialize();
        $this->assertTrue($initialized);
    }

    public function testApply()
    {
        $preTransitioned  = false;
        $postTransitioned = false;
        $that             = $this;
        $this->dispatcher->addListener(
            FiniteEvents::PRE_TRANSITION,
            function(StateMachineEvent $event) use(&$preTransitioned, $that) {
                $preTransitioned = true;
                $that->assertSame($that->object, $event->getStateMachine());
            }
        );
        $this->dispatcher->addListener(
            FiniteEvents::POST_TRANSITION,
            function(TransitionEvent $event) use(&$postTransitioned, $that) {
                $postTransitioned = true;
                $that->assertSame($that->object, $event->getStateMachine());
            }
        );
        $this->initialize();
        $this->object->apply('t23');
        $this->assertTrue($preTransitioned);
        $this->assertTrue($postTransitioned);
    }
}

