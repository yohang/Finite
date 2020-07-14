<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\Callback;
use PHPUnit\Framework\TestCase;
use Finite\Event\Callback\CallbackSpecification;
use Finite\Event\TransitionEvent;
use Finite\StateMachine\StateMachine;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackTest extends TestCase
{
    public function testInvokeWithGoodSpec()
    {
        $spec         = $this->getMockBuilder(CallbackSpecification::class)->disableOriginalConstructor()->getMock();
        $callableMock = $this->getMockBuilder(\stdClass::class)->setMethods(array('call'))->getMock();
        $event        = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateMachine = $this->getMockBuilder(StateMachine::class)->disableOriginalConstructor()->getMock();

        $event->expects($this->once())->method('getStateMachine')->will($this->returnValue($stateMachine));
        $spec->expects($this->once())->method('isSatisfiedBy')->with($event)->will($this->returnValue(true));

        $callableMock->expects($this->once())->method('call');

        $callback = new Callback($spec, array($callableMock, 'call'));
        $callback($event);
    }

    public function testInvokeWithBadSpec()
    {
        $spec         = $this->getMockBuilder(CallbackSpecification::class)->disableOriginalConstructor()->getMock();
        $callableMock = $this->getMockBuilder(\stdClass::class)->setMethods(array('call'))->getMock();
        $event        = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();

        $spec->expects($this->once())->method('isSatisfiedBy')->with($event)->will($this->returnValue(false));
        $callableMock->expects($this->never())->method('call');

        $callback = new Callback($spec, array($callableMock, 'call'));
        $callback($event);
    }
}
