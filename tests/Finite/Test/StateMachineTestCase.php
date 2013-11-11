<?php

namespace Finite\Test;

use Finite\State\State;
use  Finite\StateMachine\StateMachine;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachineTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StateMachine
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    protected function setUp()
    {
        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new StateMachine(null, $this->dispatcher);
    }

    public function statesProvider()
    {
        return array(
            array(new State('s1', State::TYPE_INITIAL)),
            array('s2'),
            array('s3'),
            array('s4'),
            array('s5'),
        );
    }

    public function transitionsProvider()
    {
        return array(
            array('t12', 's1', 's2'),
            array('t23', 's2', 's3'),
            array('t34', 's3', 's4'),
            array('t45', 's4', 's5'),
        );
    }

    protected function addStates()
    {
        foreach ($this->statesProvider() as $state) {
            $this->object->addState($state[0]);
        }
    }

    protected function addTransitions()
    {
        foreach ($this->transitionsProvider() as $transitions) {
            $this->object->addTransition($transitions[0], $transitions[1], $transitions[2]);
        }
    }

    protected function initialize()
    {
        $this->addStates();
        $this->addTransitions();
        $this->object->setObject($this->getStatefulObjectMock());
        $this->object->initialize();
    }

    protected function getStatefulObjectMock()
    {
        $mock = $this->getMock('Finite\StatefulInterface');
        $mock
            ->expects($this->once())
            ->method('getFiniteState')
            ->will($this->returnValue('s2'));

        return $mock;
    }
}
