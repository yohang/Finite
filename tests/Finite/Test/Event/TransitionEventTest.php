<?php

namespace Finite\Test\Event;

use Finite\Event\TransitionEvent;

class TransitionEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Finite\Transition\Transition
     */
    protected $transition;

    /**
     * @var TransitionEvent
     */
    protected $object;

    protected function setUp()
    {
        $this->transition = $this->getMockBuilder('Finite\Transition\Transition')->disableOriginalConstructor()->getMock();

        $this->transition
            ->expects($this->once())
            ->method('resolveProperties')
            ->with($this->isType('array'))
            ->will($this->returnValue(array('returned' => 1)));

        $this->object = new TransitionEvent(
            $this->getMockBuilder('Finite\State\State')->disableOriginalConstructor()->getMock(),
            $this->transition,
            $this->getMockBuilder('Finite\StateMachine\StateMachine')->disableOriginalConstructor()->getMock(),
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
