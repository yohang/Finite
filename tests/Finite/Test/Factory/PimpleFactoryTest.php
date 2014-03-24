<?php

namespace Finite\Test\Factory;

use Finite\Factory\PimpleFactory;
use  Finite\StateMachine\StateMachine;

class PimpleFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PimpleFactory
     */
    protected $object;

    protected $accessor;

    public function setUp()
    {
        $this->accessor = $accessor = $this->getMock('Finite\State\Accessor\StateAccessorInterface');
        $container = new \Pimple(array(
            'state_machine' => function() use ($accessor) {
                $sm =  new StateMachine(null, null, $accessor);
                $sm->addTransition('t12', 's1', 's2');
                $sm->addTransition('t23', 's2', 's3');

                return $sm;
            }
        ));

        $this->object = new PimpleFactory($container, 'state_machine');
    }

    public function testGet()
    {
        $object = $this->getMock('Finite\StatefulInterface');
        $this->accessor->expects($this->at(0))->method('getState')->will($this->returnValue('s2'));
        $sm = $this->object->get($object);

        $this->assertInstanceOf('Finite\StateMachine\StateMachine', $sm);
        $this->assertSame('s2', $sm->getCurrentState()->getName());

        $object2 = $this->getMock('Finite\StatefulInterface');
        $this->accessor->expects($this->at(0))->method('getState')->will($this->returnValue('s2'));
        $sm2 = $this->object->get($object2);

        $this->assertNotSame($sm, $sm2);
    }
}
