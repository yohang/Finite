<?php

namespace Finite\Test\Event;

use Finite\Event\TransitionEvent;
use Finite\State\State;
use Finite\StateMachine\StateMachine;
use Finite\Transition\Transition;
use PHPUnit_Framework_TestCase;

class TransitionEventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Finite\Transition\Transition
     */
    protected $transition;

    /**
     * @var TransitionEvent
     */
    protected $object;

    protected function setUp()
    {
        $this->transition = $this->getMockBuilder(Transition::class)->disableOriginalConstructor()->getMock();

        $this->transition
            ->expects($this->once())
            ->method('resolveProperties')
            ->with(...[$this->isType('array')])
            ->willReturn(['returned' => 1])
        ;

        $this->object = new TransitionEvent(
            $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock(),
            $this->transition,
            $this->getMockBuilder(StateMachine::class)->disableOriginalConstructor()->getMock(),
            []
        );
    }

    public function testItResolveProperties()
    {
        $this->assertSame(['returned' => 1], $this->object->getProperties());
    }

    public function testPropertyGetters()
    {
        $this->assertSame(1, $this->object->get('returned'));
        $this->assertTrue($this->object->has('returned'));
        $this->assertNull($this->object->get('foo', null));
    }
}
