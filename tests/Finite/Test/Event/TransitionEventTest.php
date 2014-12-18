<?php

namespace Finite\Test\Event;

use Finite\Event\TransitionEvent;

class TransitionEventTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionsResolver()
    {
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolver');
        $resolver->expects($this->once())->method('resolve')->will($this->returnValue(array('returned' => 1)));

        $transition = $this->getMockBuilder('Finite\Transition\Transition')->disableOriginalConstructor()->getMock();
        $transition->expects($this->exactly(2))->method('getEventOptionsResolver')->will($this->returnValue($resolver));

        $event = new TransitionEvent(
            $this->getMockBuilder('Finite\State\State')->disableOriginalConstructor()->getMock(),
            $transition,
            $this->getMockBuilder('Finite\StateMachine\StateMachine')->disableOriginalConstructor()->getMock(),
            array()
        );

        $this->assertSame(array('returned' => 1), $event->getParameters());
    }
}
