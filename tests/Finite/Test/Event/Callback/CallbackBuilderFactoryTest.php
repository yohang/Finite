<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\CallbackBuilderFactory;
use PHPUnit\Framework\TestCase;
use Finite\StateMachine\StateMachineInterface;
use Finite\Event\Callback\CallbackBuilder;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackBuilderFactoryTest extends TestCase
{
    public function testItConstructsCallbackBuilder()
    {
        $sm = $this->getMockBuilder(StateMachineInterface::class)->getMock();

        $factory = new CallbackBuilderFactory;

        $this->assertInstanceOf(CallbackBuilder::class, $builder = $factory->createBuilder($sm));
        $this->assertNotSame($builder, $factory->createBuilder($sm));
    }
}
