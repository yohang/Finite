<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\Callback;
use Finite\Event\Callback\CallbackSpecification;
use Finite\Event\TransitionEvent;
use Finite\StateMachine\StateMachine;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackTest extends TestCase
{
    public function testInvokeWithGoodSpec()
    {
        $spec = $this->getMockBuilder(CallbackSpecification::class)->disableOriginalConstructor()->getMock();
        $callableMock = $this->getMockBuilder(stdClass::class)->setMethods(['call'])->getMock();
        $event = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();
        $stateMachine = $this->getMockBuilder(StateMachine::class)->disableOriginalConstructor()->getMock();

        $event->expects($this->once())->method('getStateMachine')->willReturn($stateMachine);
        $spec->expects($this->once())->method('isSatisfiedBy')->with(...[$event])->willReturn(true);

        $callableMock->expects($this->once())->method('call');

        $callback = new Callback($spec, [$callableMock, 'call']);
        $callback($event);
    }

    public function testInvokeWithBadSpec()
    {
        $spec = $this->getMockBuilder(CallbackSpecification::class)->disableOriginalConstructor()->getMock();
        $callableMock = $this->getMockBuilder(stdClass::class)->setMethods(['call'])->getMock();
        $event = $this->getMockBuilder(TransitionEvent::class)->disableOriginalConstructor()->getMock();

        $spec->expects($this->once())->method('isSatisfiedBy')->with(...[$event])->willReturn(false);
        $callableMock->expects($this->never())->method('call');

        $callback = new Callback($spec, [$callableMock, 'call']);
        $callback($event);
    }
}
