<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\CallbackBuilderFactory;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackBuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItConstructsCallbackBuilder()
    {
        $sm = $this->createMock('Finite\StateMachine\StateMachineInterface');

        $factory = new CallbackBuilderFactory;

        $this->assertInstanceOf('Finite\Event\Callback\CallbackBuilder', $builder = $factory->createBuilder($sm));
        $this->assertNotSame($builder, $factory->createBuilder($sm));
    }
}
