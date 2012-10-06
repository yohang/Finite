<?php

namespace Finite\Test;

use Finite\StateMachine;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StateMachine
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new StateMachine();
    }

    public function testAddState()
    {
        $this->object->addState('foo');
        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getState('foo'));

        $stateMock = $this->getMock('Finite\State\StateInterface');
        $stateMock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $this->object->addState($stateMock);
        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getState('bar'));
    }

    public function testAddTransition()
    {
        $this->object->addTransition('t12', 'state1', 'state2');
        $this->assertInstanceOf('Finite\Transition\TransitionInterface', $this->object->getTransition('t12'));

        $transitionMock = $this->getMock('Finite\Transition\TransitionInterface');

        $transitionMock->expects($this->atLeastOnce())->method('getName')         ->will($this->returnValue('t23'));
        $transitionMock->expects($this->once())       ->method('getInitialStates')->will($this->returnValue(array('state2')));
        $transitionMock->expects($this->atLeastOnce())->method('getState')        ->will($this->returnValue('state3'));

        $this->object->addTransition($transitionMock);
        $this->assertInstanceOf('Finite\Transition\TransitionInterface', $this->object->getTransition('t23'));

        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getState('state3'));
    }

    public function testInitialize()
    {
        $this->initialize();
    }

    public function testGetCurrentState()
    {
        $this->initialize();
        $this->assertInstanceOf('Finite\State\StateInterface', $this->object->getCurrentState());
        $this->assertSame('s2', $this->object->getCurrentState()->getName());
    }

    public function testCan()
    {
        $this->initialize();
        $this->assertTrue($this->object->can('t23'));
        $this->assertFalse($this->object->can('t34'));
    }

    /**
     * @expectedException Finite\Exception\StateException
     */
    public function testApply()
    {
        $this->initialize();
        $this->object->apply('t23');
        $this->assertSame('s3', $this->object->getCurrentState()->getName());
        $this->object->apply('t23');
    }

    private function initialize()
    {
        $this->addStates();
        $this->addTransitions();
        $this->object->setObject($this->getStatefulObjectMock());
        $this->object->initialize();
    }

    private function getStatefulObjectMock()
    {
        $mock = $this->getMock('Finite\StatefulInterface');
        $mock
            ->expects($this->once())
            ->method('getFiniteState')
            ->will($this->returnValue('s2'));

        return $mock;
    }

    public function statesProvider()
    {
        return array(
            array('s1'),
            array('s2'),
            array('s3'),
            array('s3'),
            array('s4'),
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

    private function addStates()
    {
        foreach ($this->statesProvider() as $state) {
            $this->object->addState($state[0]);
        }
    }

    private function addTransitions()
    {
        foreach ($this->transitionsProvider() as $transitions) {
            $this->object->addTransition($transitions[0], $transitions[1], $transitions[2]);
        }
    }
}
