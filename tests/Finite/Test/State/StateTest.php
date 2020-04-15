<?php

namespace Finite\Test\State;

use Finite\State\State;
use Finite\Transition\TransitionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateTest extends TestCase
{
    /**
     * @var State
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new State('test');
    }

    public function testSetTransitions(): void
    {
        $this->assertCount(0, $this->object->getTransitions());

        $this->object->setTransitions(['transition-1', 'transition-2']);

        $this->assertCount(2, $this->object->getTransitions());
    }

    public function testAddTransition(): void
    {
        $this->object->addTransition($this->getTransitionMock('transition-1'));
        $this->object->addTransition($this->getTransitionMock('transition-2'));
        $this->object->addTransition('transition-3');

        $this->assertEquals(0, array_search('transition-1', $this->object->getTransitions(), true));
        $this->assertEquals(1, array_search('transition-2', $this->object->getTransitions(), true));
        $this->assertEquals(2, array_search('transition-3', $this->object->getTransitions(), true));
    }

    /**
     * @depends      testAddTransition
     * @dataProvider canDataProvider
     * @param $transitions
     * @param $can
     * @param $cannot
     */
    public function testCan($transitions, $can, $cannot): void
    {
        foreach ($transitions as $transition) {
            $this->object->addTransition($transition);
        }

        $this->assertTrue($this->object->can($can));
        $this->assertFalse($this->object->can($cannot));
    }

    public function canDataProvider(): array
    {
        return [
            [['t1', 't2', 't3'], 't3', 't4'],
            [['t1', 't2'], 't2', 't3'],
        ];
    }

    /**
     * @param string $transitionName
     *
     * @return \Finite\Transition\TransitionInterface|\PHPUnit\Framework\MockObject\Builder\InvocationMocker|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getTransitionMock($transitionName)
    {
        $transition = $this->createMock(TransitionInterface::class);

        $transition->expects($this->once())
            ->method('getName')
            ->willReturn($transitionName)
        ;

        return $transition;
    }

    /**
     * @covers \Finite\State\State::setProperties
     */
    public function testSetProperties(): void
    {
        $state = new State('state');

        $state->setProperties(['a', 'b']);

        $this->assertEquals(['a', 'b'], $state->getProperties());
    }
}
