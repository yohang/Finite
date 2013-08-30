<?php

namespace Finite\Test\Event;

use Finite\Event\CallbackHandler;
use Finite\Event\FiniteEvents;
use Finite\Event\TransitionEvent;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CallbackHandler
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateful;

    public function setUp()
    {
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $this->stateful   = $this->getMock('Finite\StatefulInterface');
        $this->object     = new CallbackHandler($this->dispatcher);
    }

    public function testItAttachsAllPreTransition()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('addListener')
            ->with(FiniteEvents::PRE_TRANSITION, $this->isInstanceOf('Closure'));

        $this->object->addBefore($this->stateful, function() {});
    }

    public function testItAttachsGivenPreTransition()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('addListener')
            ->with(FiniteEvents::PRE_TRANSITION.'.t12', $this->isInstanceOf('Closure'));

        $this->object->addBefore($this->stateful, function() {}, array('on' => 't12'));
    }

    public function testItAttachsPreTransitionWithToSpec()
    {
        $sm              = $this->getMock('Finite\StateMachine\StateMachineInterface');
        $transitionOk    = $this->getMock('Finite\Transition\TransitionInterface');
        $transitionNotOk = $this->getMock('Finite\Transition\TransitionInterface');
        $state           = $this->getMock('Finite\State\StateInterface');
        $e1              = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $e2              = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();

        $e1->expects($this->any())->method('getStateMachine')->will($this->returnValue($sm));
        $e2->expects($this->any())->method('getStateMachine')->will($this->returnValue($sm));
        $e1->expects($this->any())->method('getInitialState')->will($this->returnValue($state));
        $sm->expects($this->any())->method('getObject')->will($this->returnValue($this->stateful));
        $transitionOk->expects($this->any())->method('getState')->will($this->returnValue('foobar'));
        $transitionNotOk->expects($this->any())->method('getState')->will($this->returnValue('bazqux'));
        $e1->expects($this->any())->method('getTransition')->will($this->returnValue($transitionOk));
        $e2->expects($this->any())->method('getTransition')->will($this->returnValue($transitionNotOk));

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addListener')
            ->with(
                FiniteEvents::PRE_TRANSITION,
                $this->logicalAnd(
                    $this->isInstanceOf('Closure'),
                    $this->callback(function(\Closure $c) use ($e1, $e2) { $c($e1); $c($e2); return true; })
                )
            );

        $that    = $this;
        $this->object->addBefore(
            $this->stateful,
            function(TransitionEvent $event) use ($that, &$counter) {
                $that->assertSame('foobar', $event->getTransition()->getState());
            },
            array('to' => 'foobar')
        );
    }

    public function testItAttachsPreTransitionWithFromSpec()
    {
        $sm              = $this->getMock('Finite\StateMachine\StateMachineInterface');
        $e1              = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $e2              = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $stateOk         = $this->getMock('Finite\State\StateInterface');
        $stateNotOk      = $this->getMock('Finite\State\StateInterface');
        $transition      = $this->getMock('Finite\Transition\TransitionInterface');

        $e1->expects($this->any())->method('getStateMachine')->will($this->returnValue($sm));
        $e2->expects($this->any())->method('getStateMachine')->will($this->returnValue($sm));
        $e1->expects($this->any())->method('getTransition')->will($this->returnValue($transition));
        $e2->expects($this->any())->method('getTransition')->will($this->returnValue($transition));
        $sm->expects($this->any())->method('getObject')->will($this->returnValue($this->stateful));
        $e1->expects($this->any())->method('getInitialState')->will($this->returnValue($stateOk));
        $e2->expects($this->any())->method('getInitialState')->will($this->returnValue($stateNotOk));
        $stateOk->expects($this->any())->method('getName')->will($this->returnValue('foobar'));
        $stateNotOk->expects($this->any())->method('getName')->will($this->returnValue('bazqux'));

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addListener')
            ->with(
                FiniteEvents::PRE_TRANSITION,
                $this->logicalAnd(
                    $this->isInstanceOf('Closure'),
                    $this->callback(function(\Closure $c) use ($e1, $e2) { $c($e1); $c($e2); return true; })
                )
            );

        $that = $this;
        $this->object->addBefore(
            $this->stateful,
            function(TransitionEvent $event) use ($that, &$counter) {
                $that->assertSame('foobar', $event->getInitialState()->getName());
            },
            array('from' => 'foobar')
        );
    }

    public function testItAttachsPreTransitionWithExcludeFromSpec()
    {
        $sm              = $this->getMock('Finite\StateMachine\StateMachineInterface');
        $e1              = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $e2              = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $stateOk         = $this->getMock('Finite\State\StateInterface');
        $stateNotOk      = $this->getMock('Finite\State\StateInterface');
        $transition      = $this->getMock('Finite\Transition\TransitionInterface');

        $e1->expects($this->any())->method('getStateMachine')->will($this->returnValue($sm));
        $e2->expects($this->any())->method('getStateMachine')->will($this->returnValue($sm));
        $e1->expects($this->any())->method('getTransition')->will($this->returnValue($transition));
        $e2->expects($this->any())->method('getTransition')->will($this->returnValue($transition));
        $sm->expects($this->any())->method('getObject')->will($this->returnValue($this->stateful));
        $e1->expects($this->any())->method('getInitialState')->will($this->returnValue($stateOk));
        $e2->expects($this->any())->method('getInitialState')->will($this->returnValue($stateNotOk));
        $stateOk->expects($this->any())->method('getName')->will($this->returnValue('foobar'));
        $stateNotOk->expects($this->any())->method('getName')->will($this->returnValue('bazqux'));

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addListener')
            ->with(
                FiniteEvents::PRE_TRANSITION,
                $this->logicalAnd(
                    $this->isInstanceOf('Closure'),
                    $this->callback(function(\Closure $c) use ($e1, $e2) { $c($e1); $c($e2); return true; })
                )
            );

        $that = $this;
        $this->object->addBefore(
            $this->stateful,
            function(TransitionEvent $event) use ($that, &$counter) {
                $that->assertSame('foobar', $event->getInitialState()->getName());
            },
            array('from' => array('all', '-bazqux'))
        );
    }

    public function testItAttachsPreTransitionWithExcludeToSpec()
    {
        $sm              = $this->getMock('Finite\StateMachine\StateMachineInterface');
        $transitionOk    = $this->getMock('Finite\Transition\TransitionInterface');
        $transitionNotOk = $this->getMock('Finite\Transition\TransitionInterface');
        $state           = $this->getMock('Finite\State\StateInterface');
        $e1              = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();
        $e2              = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();

        $e1->expects($this->any())->method('getStateMachine')->will($this->returnValue($sm));
        $e2->expects($this->any())->method('getStateMachine')->will($this->returnValue($sm));
        $e1->expects($this->any())->method('getInitialState')->will($this->returnValue($state));
        $e2->expects($this->any())->method('getInitialState')->will($this->returnValue($state));
        $sm->expects($this->any())->method('getObject')->will($this->returnValue($this->stateful));
        $transitionOk->expects($this->any())->method('getState')->will($this->returnValue('foobar'));
        $transitionNotOk->expects($this->any())->method('getState')->will($this->returnValue('bazqux'));
        $e1->expects($this->any())->method('getTransition')->will($this->returnValue($transitionOk));
        $e2->expects($this->any())->method('getTransition')->will($this->returnValue($transitionNotOk));

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addListener')
            ->with(
                FiniteEvents::PRE_TRANSITION,
                $this->logicalAnd(
                    $this->isInstanceOf('Closure'),
                    $this->callback(function(\Closure $c) use ($e1, $e2) { $c($e1); $c($e2); return true; })
                )
            );

        $that    = $this;
        $this->object->addBefore(
            $this->stateful,
            function(TransitionEvent $event) use ($that, &$counter) {
                $that->assertSame('foobar', $event->getTransition()->getState());
            },
            array('to' => '-bazqux')
        );
    }
}
