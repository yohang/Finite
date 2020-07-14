<?php

namespace Finite\Test\Event;

use Finite\Event\Callback\Callback;
use Finite\Event\Callback\CallbackBuilder;
use Finite\Event\CallbackHandler;
use Finite\Event\FiniteEvents;
use Finite\Event\TransitionEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Finite\StateMachine\StateMachineInterface;
use Finite\Transition\TransitionInterface;
use Finite\State\StateInterface;
use Finite\StatefulInterface;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackHandlerTest extends TestCase
{
    /**
     * @var CallbackHandler
     */
    protected $object;

    protected $dispatcher;

    protected $statemachine;

    public function setUp(): void
    {
        $this->dispatcher   = $this->getMockBuilder(EventDispatcher::class)->getMock();
        $this->statemachine = $this->getMockBuilder(StateMachineInterface::class)->getMock();
        $this->object       = new CallbackHandler($this->dispatcher);
    }

    public function testItAttachsAllPreTransition()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('addListener')
            ->with(FiniteEvents::PRE_TRANSITION, $this->isInstanceOf(Callback::class));

        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)->setCallable(function() {})->getCallback()
        );
    }

    public function testItAttachsGivenPreTransition()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('addListener')
            ->with(FiniteEvents::PRE_TRANSITION, $this->isInstanceOf('Finite\Event\Callback\Callback'));

        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->setCallable(function() {})
                ->addOn('t12')
                ->getCallback()
        );
    }

    public function testItAttachsPreTransitionWithToSpec()
    {
        $transitionOk    = $this->getMockBuilder(TransitionInterface::class)->getMock();
        $transitionNotOk = $this->getMockBuilder(TransitionInterface::class)->getMock();
        $state           = $this->getMockBuilder(StateInterface::class)->getMock();
        $e1              = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $e2              = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateful        = $this->getMockBuilder(StatefulInterface::class)->getMock();

        $this->statemachine->expects($this->any())->method('getObject')->will($this->returnValue($stateful));
        $e1->expects($this->any())->method('getStateMachine')->will($this->returnValue($this->statemachine));
        $e2->expects($this->any())->method('getStateMachine')->will($this->returnValue($this->statemachine));
        $e1->expects($this->any())->method('getInitialState')->will($this->returnValue($state));
        $e2->expects($this->any())->method('getInitialState')->will($this->returnValue($state));
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
                    $this->isInstanceOf(Callback::class),
                    $this->callback(function(Callback $c) use ($e1, $e2) { $c($e1); $c($e2); return true; })
                )
            );

        $that = $this;
        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->addTo('foobar')
                ->setCallable(
                    function($object, TransitionEvent $event) use ($that, $stateful) {
                        $that->assertSame('foobar', $event->getTransition()->getState());
                    }
                )
                ->getCallback()
        );
    }

    public function testItAttachsPreTransitionWithFromSpec()
    {
        $e1              = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $e2              = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateOk         = $this->getMockBuilder(StateInterface::class)->getMock();
        $stateNotOk      = $this->getMockBuilder(StateInterface::class)->getMock();
        $transition      = $this->getMockBuilder(TransitionInterface::class)->getMock();
        $stateful        = $this->getMockBuilder(StatefulInterface::class)->getMock();

        $this->statemachine->expects($this->any())->method('getObject')->will($this->returnValue($stateful));
        $e1->expects($this->any())->method('getStateMachine')->will($this->returnValue($this->statemachine));
        $e2->expects($this->any())->method('getStateMachine')->will($this->returnValue($this->statemachine));
        $e1->expects($this->any())->method('getTransition')->will($this->returnValue($transition));
        $e2->expects($this->any())->method('getTransition')->will($this->returnValue($transition));
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
                    $this->isInstanceOf('Finite\Event\Callback\Callback'),
                    $this->callback(function(Callback $c) use ($e1, $e2) { $c($e1); $c($e2); return true; })
                )
            );

        $that = $this;
        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->addFrom('foobar')
                ->setCallable(
                    function($object, TransitionEvent $event) use ($that, $stateful) {
                        $that->assertSame('foobar', $event->getInitialState()->getName());
                    }
                )
                ->getCallback()
        );
    }

    public function testItAttachsPreTransitionWithExcludeFromSpec()
    {
        $e1              = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $e2              = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateOk         = $this->getMockBuilder(StateInterface::class)->getMock();
        $stateNotOk      = $this->getMockBuilder(StateInterface::class)->getMock();
        $transition      = $this->getMockBuilder(TransitionInterface::class)->getMock();
        $stateful        = $this->getMockBuilder(StatefulInterface::class)->getMock();

        $this->statemachine->expects($this->any())->method('getObject')->will($this->returnValue($stateful));
        $e1->expects($this->any())->method('getStateMachine')->will($this->returnValue($this->statemachine));
        $e2->expects($this->any())->method('getStateMachine')->will($this->returnValue($this->statemachine));
        $e1->expects($this->any())->method('getTransition')->will($this->returnValue($transition));
        $e2->expects($this->any())->method('getTransition')->will($this->returnValue($transition));
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
                    $this->isInstanceOf('Finite\Event\Callback\Callback'),
                    $this->callback(function(Callback $c) use ($e1, $e2) { $c($e1); $c($e2); return true; })
                )
            );

        $that = $this;;
        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->addFrom('-bazqux')
                ->setCallable(
                    function ($object, TransitionEvent $event) use ($that, $stateful) {
                        $that->assertSame('foobar', $event->getInitialState()->getName());
                    }
                )
                ->getCallback()
        );
    }

    public function testItAttachsPreTransitionWithExcludeToSpec()
    {
        $transitionOk    = $this->getMockBuilder(TransitionInterface::class)->getMock();
        $transitionNotOk = $this->getMockBuilder(TransitionInterface::class)->getMock();
        $state           = $this->getMockBuilder(StateInterface::class)->getMock();
        $e1              = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $e2              = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateful        = $this->getMockBuilder(StatefulInterface::class)->getMock();

        $this->statemachine->expects($this->any())->method('getObject')->will($this->returnValue($stateful));
        $e1->expects($this->any())->method('getStateMachine')->will($this->returnValue($this->statemachine));
        $e2->expects($this->any())->method('getStateMachine')->will($this->returnValue($this->statemachine));
        $e1->expects($this->any())->method('getInitialState')->will($this->returnValue($state));
        $e2->expects($this->any())->method('getInitialState')->will($this->returnValue($state));
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
                    $this->isInstanceOf('Finite\Event\Callback\Callback'),
                    $this->callback(function(Callback $c) use ($e1, $e2) { $c($e1); $c($e2); return true; })
                )
            );

        $that = $this;
        $this->object->addBefore(
            CallbackBuilder::create($this->statemachine)
                ->addTo('-bazqux')
                ->setCallable(
                    function($object, TransitionEvent $event) use ($that, $stateful) {
                        $that->assertSame($object, $stateful);
                        $that->assertSame('foobar', $event->getTransition()->getState());
                    }
                )
                ->getCallback()
        );
    }
}
