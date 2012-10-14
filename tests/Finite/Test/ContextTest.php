<?php

namespace Finite\Test;

use Finite\Context;
use Finite\Factory\PimpleFactory;
use Finite\StateMachine;
use Finite\State\State;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    protected $object;

    public function setUp()
    {
        $container = new \Pimple(array(
            'state_machine' => function() {
                $sm =  new StateMachine;
                $sm->addState(new State('s1', State::TYPE_INITIAL, array(), array('foo', 'bar')));
                $sm->addTransition('t12', 's1', 's2');
                $sm->addTransition('t23', 's2', 's3');

                return $sm;
            }
        ));

        $this->object = new Context(new PimpleFactory($container, 'state_machine'));
    }

    public function testGetStateMachine()
    {
        $sm = $this->object->getStateMachine($this->getObjectMock());

        $this->assertInstanceOf('Finite\StateMachine', $sm);
        $this->assertSame('s1', $sm->getCurrentState()->getName());
    }

    public function testGetState()
    {
        $this->assertSame('s1', $this->object->getState($this->getObjectMock()));
    }

    public function testGetTransitions()
    {
        $this->assertEquals(array('t12'), $this->object->getTransitions($this->getObjectMock()));
    }

    public function testGetProperties()
    {
        $this->assertEquals(array('foo', 'bar'), $this->object->getProperties($this->getObjectMock()));
    }

    public function testHasProperty()
    {
        $this->assertTrue($this->object->hasProperty($this->getObjectMock(), 'foo'));
        $this->assertFalse($this->object->hasProperty($this->getObjectMock(), 'baz'));
    }

    private function getObjectMock()
    {
        $object = $this->getMock('Finite\StatefulInterface');
        $object->expects($this->once())->method('getFiniteState')->will($this->returnValue('s1'));

        return $object;
    }
}
