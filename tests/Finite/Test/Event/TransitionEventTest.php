<?php

namespace Finite\Test\Event;

use Finite\Event\TransitionEvent;
use PHPUnit\Framework\TestCase;
use Finite\Transition\Transition;
use Finite\State\State;
use Finite\StateMachine\StateMachine;

class TransitionEventTest extends TestCase
{
    /**
     * @var Finite\Transition\Transition
     */
    protected $transition;

    /**
     * @var TransitionEvent
     */
    protected $object;

    protected function setUp(): void
    {
        $this->transition = $this->getMockBuilder(Transition::class)->disableOriginalConstructor()->getMock();

        $this->transition
            ->expects($this->once())
            ->method('resolveProperties')
            ->with($this->isType('array'))
            ->will($this->returnValue(array('returned' => 1)));

        $this->object = new TransitionEvent(
            $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock(),
            $this->transition,
            $this->getMockBuilder(StateMachine::class)->disableOriginalConstructor()->getMock(),
            array()
        );
    }

    public function testItResolveProperties()
    {
        $this->assertSame(array('returned' => 1), $this->object->getProperties());
    }

    public function testPropertyGetters()
    {
        $this->assertSame(1, $this->object->get('returned'));
        $this->assertTrue($this->object->has('returned'));
        $this->assertNull($this->object->get('foo', null));
    }
}
