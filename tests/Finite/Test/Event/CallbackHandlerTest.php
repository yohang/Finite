<?php

namespace Finite\Test\Event;

use Finite\Event\Callback\Callback;
use Finite\Event\Callback\CallbackBuilder;
use Finite\Event\CallbackHandler;
use Finite\Event\FiniteEvents;
use Finite\Event\TransitionEvent;
use Finite\State\StateInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachineInterface;
use Finite\Transition\TransitionInterface;
use PHPUnit_Framework_TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackHandlerTest extends PHPUnit_Framework_TestCase
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
    protected $statemachine;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->statemachine = $this->createMock(StateMachineInterface::class);
        $this->object = new CallbackHandler($this->dispatcher);
    }

    public function testItAttachsAllPreTransition()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('addListener')
            ->with(...[FiniteEvents::PRE_TRANSITION, $this->isInstanceOf(Callback::class)])
        ;

        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)->setCallable(
                static function () {
                }
            )->getCallback()
        );
    }

    public function testItAttachsGivenPreTransition()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('addListener')
            ->with(...[FiniteEvents::PRE_TRANSITION, $this->isInstanceOf(Callback::class)])
        ;

        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->setCallable(
                    static function () {
                    }
                )
                ->addOn('t12')
                ->getCallback()
        );
    }

    public function testItAttachsPreTransitionWithToSpec()
    {
        $transitionOk = $this->createMock(TransitionInterface::class);
        $transitionNotOk = $this->createMock(TransitionInterface::class);
        $state = $this->createMock(StateInterface::class);
        $e1 = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $e2 = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateful = $this->createMock(StatefulInterface::class);

        $this->statemachine->method('getObject')->willReturn($stateful);
        $e1->method('getStateMachine')->willReturn($this->statemachine);
        $e2->method('getStateMachine')->willReturn($this->statemachine);
        $e1->method('getInitialState')->willReturn($state);
        $e2->method('getInitialState')->willReturn($state);
        $transitionOk->method('getState')->willReturn('foobar');
        $transitionNotOk->method('getState')->willReturn('bazqux');
        $e1->method('getTransition')->willReturn($transitionOk);
        $e2->method('getTransition')->willReturn($transitionNotOk);

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addListener')
            ->with(
                ...[
                       FiniteEvents::PRE_TRANSITION,
                       $this->logicalAnd(
                           $this->isInstanceOf(Callback::class),
                           $this->callback(
                               static function (Callback $c) use ($e1, $e2) {
                                   $c($e1);
                                   $c($e2);

                                   return true;
                               }
                           )
                       ),
                   ]
            )
        ;

        $that = $this;
        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->addTo('foobar')
                ->setCallable(
                    static function ($object, TransitionEvent $event) use ($that, $stateful) {
                        $that->assertSame('foobar', $event->getTransition()->getState());
                    }
                )
                ->getCallback()
        );
    }

    public function testItAttachsPreTransitionWithFromSpec()
    {
        $e1 = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $e2 = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateOk = $this->createMock(StateInterface::class);
        $stateNotOk = $this->createMock(StateInterface::class);
        $transition = $this->createMock(TransitionInterface::class);
        $stateful = $this->createMock(StatefulInterface::class);

        $this->statemachine->method('getObject')->willReturn($stateful);
        $e1->method('getStateMachine')->willReturn($this->statemachine);
        $e2->method('getStateMachine')->willReturn($this->statemachine);
        $e1->method('getTransition')->willReturn($transition);
        $e2->method('getTransition')->willReturn($transition);
        $e1->method('getInitialState')->willReturn($stateOk);
        $e2->method('getInitialState')->willReturn($stateNotOk);
        $stateOk->method('getName')->willReturn('foobar');
        $stateNotOk->method('getName')->willReturn('bazqux');

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addListener')
            ->with(
                ...[
                       FiniteEvents::PRE_TRANSITION,
                       $this->logicalAnd(
                           $this->isInstanceOf(Callback::class),
                           $this->callback(
                               static function (Callback $c) use ($e1, $e2) {
                                   $c($e1);
                                   $c($e2);

                                   return true;
                               }
                           )
                       ),
                   ]
            )
        ;

        $that = $this;
        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->addFrom('foobar')
                ->setCallable(
                    static function ($object, TransitionEvent $event) use ($that, $stateful) {
                        $that->assertSame('foobar', $event->getInitialState()->getName());
                    }
                )
                ->getCallback()
        );
    }

    public function testItAttachsPreTransitionWithExcludeFromSpec()
    {
        $e1 = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $e2 = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateOk = $this->createMock(StateInterface::class);
        $stateNotOk = $this->createMock(StateInterface::class);
        $transition = $this->createMock(TransitionInterface::class);
        $stateful = $this->createMock(StatefulInterface::class);

        $this->statemachine->method('getObject')->willReturn($stateful);
        $e1->method('getStateMachine')->willReturn($this->statemachine);
        $e2->method('getStateMachine')->willReturn($this->statemachine);
        $e1->method('getTransition')->willReturn($transition);
        $e2->method('getTransition')->willReturn($transition);
        $e1->method('getInitialState')->willReturn($stateOk);
        $e2->method('getInitialState')->willReturn($stateNotOk);
        $stateOk->method('getName')->willReturn('foobar');
        $stateNotOk->method('getName')->willReturn('bazqux');

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addListener')
            ->with(
                ...[
                       FiniteEvents::PRE_TRANSITION,
                       $this->logicalAnd(
                           $this->isInstanceOf(Callback::class),
                           $this->callback(
                               static function (Callback $c) use ($e1, $e2) {
                                   $c($e1);
                                   $c($e2);

                                   return true;
                               }
                           )
                       ),
                   ]
            )
        ;

        $that = $this;
        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->addFrom('-bazqux')
                ->setCallable(
                    static function ($object, TransitionEvent $event) use ($that, $stateful) {
                        $that->assertSame('foobar', $event->getInitialState()->getName());
                    }
                )
                ->getCallback()
        );
    }

    public function testItAttachsPreTransitionWithExcludeToSpec()
    {
        $transitionOk = $this->createMock(TransitionInterface::class);
        $transitionNotOk = $this->createMock(TransitionInterface::class);
        $state = $this->createMock(StateInterface::class);
        $e1 = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $e2 = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateful = $this->createMock(StatefulInterface::class);

        $this->statemachine->method('getObject')->willReturn($stateful);
        $e1->method('getStateMachine')->willReturn($this->statemachine);
        $e2->method('getStateMachine')->willReturn($this->statemachine);
        $e1->method('getInitialState')->willReturn($state);
        $e2->method('getInitialState')->willReturn($state);
        $transitionOk->method('getState')->willReturn('foobar');
        $transitionNotOk->method('getState')->willReturn('bazqux');
        $e1->method('getTransition')->willReturn($transitionOk);
        $e2->method('getTransition')->willReturn($transitionNotOk);

        $this->dispatcher
            ->expects($this->at(0))
            ->method('addListener')
            ->with(
                ...[
                       FiniteEvents::PRE_TRANSITION,
                       $this->logicalAnd(
                           $this->isInstanceOf(Callback::class),
                           $this->callback(
                               static function (Callback $c) use ($e1, $e2) {
                                   $c($e1);
                                   $c($e2);

                                   return true;
                               }
                           )
                       ),
                   ]
            )
        ;

        $that = $this;
        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->addTo('-bazqux')
                ->setCallable(
                    static function ($object, TransitionEvent $event) use ($that, $stateful) {
                        $that->assertSame($object, $stateful);
                        $that->assertSame('foobar', $event->getTransition()->getState());
                    }
                )
                ->getCallback()
        );
    }
}
