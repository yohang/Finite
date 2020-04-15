<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\CallbackBuilder;
use Finite\Event\Callback\CallbackBuilderFactory;
use Finite\StateMachine\StateMachineInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackBuilderFactoryTest extends TestCase
{
    public function testItConstructsCallbackBuilder()
    {
        $sm = $this->createMock(StateMachineInterface::class);

        $factory = new CallbackBuilderFactory;

        $this->assertInstanceOf(CallbackBuilder::class, $builder = $factory->createBuilder($sm));
        $this->assertNotSame($builder, $factory->createBuilder($sm));
    }
}
