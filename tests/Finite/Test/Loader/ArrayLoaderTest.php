<?php

namespace Finite\Test\Loader;

use Finite\Loader\ArrayLoader;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ArrayLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayLoader
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new ArrayLoader(array(
            'class'       => 'Stateful1',
            'states'      => array(
                'start'  => array('type' => 'initial', 'properties' => array()),
                'middle' => array('type' => 'normal', 'properties' => array()),
                'end'    => array('type' => 'final', 'properties' => array()),
            ),
            'transitions' => array(
                'middleize' => array(
                    'from' => array('start'),
                    'to'   => 'middle'
                ),
                'finish'    => array(
                    'from' => array('middle'),
                    'to'   => 'end'
                )
            )
        ));
    }

    public function testLoad()
    {
        $sm = $this->getMock('Finite\StateMachine\StateMachine');
        $sm->expects($this->exactly(3))->method('addState');
        $sm->expects($this->exactly(2))->method('addTransition');
        $this->object->load($sm);
    }

    public function testSupports()
    {
        $object  = $this->getMock('Finite\StatefulInterface', array(), array(), 'Stateful1');
        $object2 = $this->getMock('Finite\StatefulInterface', array(), array(), 'Stateful2');

        $this->assertTrue($this->object->supports($object));
        $this->assertFalse($this->object->supports($object2));
    }
}
