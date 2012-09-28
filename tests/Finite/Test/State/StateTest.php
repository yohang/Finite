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
        $transition = $this->getMock('Finite\Transition\TransitionInterface');
        $transition
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('transition-1'))
        ;

        $this->object->addTransition($transition);
    }
}
