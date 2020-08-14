<?php

namespace Finite\Test\State;

use Finite\State\State;

/**
 *
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var State
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new State('test');
    }

    public function testAddTransition()
    {
        $this->object->addTransition($this->getTransitionMock('transition-1'));
        $this->object->addTransition($this->getTransitionMock('transition-2'));
        $this->object->addTransition('transition-3');

        $this->assertContains('transition-1', $this->object->getTransitions());
        $this->assertContains('transition-2', $this->object->getTransitions());
        $this->assertContains('transition-3', $this->object->getTransitions());
    }

    /**
     * @depends      testAddTransition
     * @dataProvider testCanDataProvider
     */
    public function testCan($transitions, $can, $cannot)
    {
        foreach ($transitions as $transition) {
            $this->object->addTransition($transition);
        }

        $this->assertTrue($this->object->can($can));
        $this->assertFalse($this->object->can($cannot));
    }

    public function testCanDataProvider()
    {
        return array(
            array(array('t1', 't2', 't3'), 't3', 't4'),
            array(array('t1', 't2'), 't2', 't3'),
        );
    }

    /**
     * @param string $transitionName
     *
     * @return \PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    private function getTransitionMock($transitionName)
    {
        $transition = $this->createMock('\Finite\Transition\TransitionInterface');

        $transition->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($transitionName))
        ;

        return $transition;
    }
}
