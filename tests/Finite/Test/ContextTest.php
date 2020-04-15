<?php

namespace Finite\Test;

use Finite\Context;
use Finite\Factory\FactoryInterface;
use Finite\Factory\PimpleFactory;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\State\State;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use Finite\StateMachine\StateMachineInterface;
use Finite\Transition\TransitionInterface;
use PHPUnit\Framework\TestCase;
use Pimple;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ContextTest extends TestCase
{
    /**
     * @var Context
     */
    protected $object;

    protected $accessor;

    public function setUp(): void
    {
        $this->accessor = $accessor = $this->createMock(StateAccessorInterface::class);
        $container = new Pimple(
            [
                'state_machine' => static function () use ($accessor) {
                    $sm = new StateMachine(null, null, $accessor);
                    $sm->addState(new State('s1', State::TYPE_INITIAL, [], ['foo' => true, 'bar' => false]));
                    $sm->addTransition('t12', 's1', 's2');
                    $sm->addTransition('t23', 's2', 's3');

                    return $sm;
                },
            ]
        );

        $this->object = new Context(new PimpleFactory($container, 'state_machine'));
    }

    public function testGetStateMachine(): void
    {
        $this->accessor->expects($this->once())->method('getState')->willReturn('s1');
        $sm = $this->object->getStateMachine($this->createMock(StatefulInterface::class));

        $this->assertInstanceOf(StateMachine::class, $sm);
        $this->assertSame('s1', $sm->getCurrentState()->getName());
    }

    public function testGetState(): void
    {
        $this->accessor->expects($this->once())->method('getState')->willReturn('s1');
        $this->assertSame('s1', $this->object->getState($this->createMock(StatefulInterface::class)));
    }

    public function testGetTransitions(): void
    {
        $this->accessor->expects($this->once())->method('getState')->willReturn('s1');
        $this->assertEquals(['t12'], $this->object->getTransitions($this->createMock(StatefulInterface::class)));
    }

    public function testGetTransitionObjects(): void
    {
        $this->accessor->expects($this->once())->method('getState')->willReturn('s1');

        $transitions = $this->object->getTransitions($this->createMock(StatefulInterface::class), 'default', true);

        $this->assertCount(1, $transitions);
        $this->assertInstanceOf(TransitionInterface::class, $transitions[0]);
    }

    public function testGetProperties(): void
    {
        $this->accessor->expects($this->once())->method('getState')->willReturn('s1');
        $this->assertEquals(
            ['foo' => true, 'bar' => false],
            $this->object->getProperties($this->createMock(StatefulInterface::class))
        );
    }

    public function testHasProperty(): void
    {
        $this->accessor->expects($this->exactly(2))->method('getState')->willReturn('s1');
        $this->assertTrue($this->object->hasProperty($this->createMock(StatefulInterface::class), 'foo'));
        $this->assertFalse($this->object->hasProperty($this->createMock(StatefulInterface::class), 'baz'));
    }

    public function testItRetrievesGoodStateMachine(): void
    {
        $object = $this->createMock(StatefulInterface::class);
        $factory = $this->createMock(FactoryInterface::class);
        $sm = $this->createMock(StateMachineInterface::class);

        $factory->expects($this->once())->method('get')->with(...[$object, 'foo'])->willReturn($sm);

        $context = new Context($factory);
        $this->assertSame($sm, $context->getStateMachine($object, 'foo'));
    }
}
