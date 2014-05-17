<?php

namespace Finite\Test\Bundle\FiniteBundle;

use Finite\Bundle\FiniteBundle\ContainerAwareCallbackHandler;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Test of ContainerCallbackPass
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ContainerAwareCallbackHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    protected $object;

    public function setUp()
    {
        $this->object = new ContainerAwareCallbackHandler(new EventDispatcher());
    }

    public function testPass()
    {
        $object    = $this->getMock('Finite\StatefulInterface');
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $event     = $this->getMockBuilder('Finite\Event\TransitionEvent')->disableOriginalConstructor()->getMock();

        $container->expects($this->once())
            ->method('has')
            ->with('my_service')
            ->will($this->returnValue(true))
        ;
        $container->expects($this->once())
            ->method('get')
            ->with('my_service')
            ->will($this->returnValue($object))
        ;
        $this->object->setContainer($container);

        $object->expects($this->once())->method('getFiniteState');

        $this->object->call(array('@my_service', 'getFiniteState'), $object, $event);
    }
}
