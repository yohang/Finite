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

    protected $accessor;

    public function setUp()
    {
        $this->accessor = $this->createMock('Finite\State\Accessor\StateAccessorInterface');
        $this->dispatcher = $this->getMockBuilder('Finite\Event\StateMachineDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new StateMachine(null, $this->dispatcher, $this->accessor);
    }

    public function statesProvider()
    {
        return array(
            array(new State('s1', State::TYPE_INITIAL)),
            array(new State('s2', State::TYPE_NORMAL, array(), array('visible' => true))),
            array('s3'),
            array(new State('s4', State::TYPE_NORMAL, array(), array('visible' => true))),
            array(new State('s5', State::TYPE_FINAL, array(), array('visible' => false))),
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
        $mock = $this->createMock('Finite\StatefulInterface');
        $this->accessor->expects($this->at(0))->method('getState')->will($this->returnValue('s2'));

        return $mock;
    }
}
