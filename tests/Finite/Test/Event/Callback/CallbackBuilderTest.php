<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\CallbackBuilder;
use PHPUnit\Framework\TestCase;
use Finite\StateMachine\StateMachine;
use Finite\Event\Callback\Callback;
use Finite\Event\Callback\CallbackSpecification;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackBuilderTest extends TestCase
{
    public function testItBuildsCallback()
    {
        $stateMachine = $this
            ->getMockBuilder(StateMachine::class)
            ->disableOriginalConstructor()
            ->getMock();

        $callableMock = $this->getMockBuilder(\stdClass::class)->setMethods(array('call'))->getMock();

        $callback = CallbackBuilder::create($stateMachine, array($callableMock, 'call'))
            ->setFrom(array('s1'))
            ->addFrom('s2')
            ->setTo(array('s2'))
            ->addTo('s3')
            ->setOn(array('t12'))
            ->addOn('t23')
            ->getCallback();

        $this->assertInstanceOf(Callback::class, $callback);
        $this->assertInstanceOf(CallbackSpecification::class, $callback->getSpecification());
    }
}
