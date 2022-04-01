<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\CallbackBuilderFactory;
use PHPUnit\Framework\TestCase;

/**
 * @author Yohan Giarelli <yohan@giarel.li>
 */
class CallbackBuilderFactoryTest extends TestCase
{
    public function testItConstructsCallbackBuilder()
    {
        $sm = $this->createMock('Finite\StateMachine\StateMachineInterface');

        $factory = new CallbackBuilderFactory;

        $this->assertInstanceOf('Finite\Event\Callback\CallbackBuilder', $builder = $factory->createBuilder($sm));
        $this->assertNotSame($builder, $factory->createBuilder($sm));
    }
}
