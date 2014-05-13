<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\CallbackBuilder;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testItBuildsCallback()
    {
        $stateMachine = $this
            ->getMockBuilder('Finite\StateMachine\StateMachine')
            ->disableOriginalConstructor()
            ->getMock();

        $callableMock = $this->getMockBuilder('\stdClass')->setMethods(array('call'))->getMock();

        $callback = CallbackBuilder::create($stateMachine, array($callableMock, 'call'))
            ->setFrom(array('s1'))
            ->addFrom('s2')
            ->setTo(array('s2'))
            ->addTo('s3')
            ->setOn(array('t12'))
            ->addOn('t23')
            ->getCallback();

        $this->assertInstanceOf('Finite\Event\Callback\Callback', $callback);
        $this->assertInstanceOf('Finite\Event\Callback\CallbackSpecification', $callback->getSpecification());
    }
}
