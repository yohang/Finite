<?php

namespace Finite\Test;

use Finite\State\Accessor\StateAccessorInterface;
use Finite\State\State;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class StateMachineTestCase extends TestCase
{
    /**
     * @var StateMachine
     */
    protected $object;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $dispatcher;

    protected $accessor;

    public function setUp(): void
    {
        $this->accessor = $this->createMock(StateAccessorInterface::class);
        $this->dispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->object = new StateMachine(null, $this->dispatcher, $this->accessor);
    }

    public function statesProvider(): array
    {
        return [
            [new State('s1', State::TYPE_INITIAL)],
            [new State('s2', State::TYPE_NORMAL, [], ['visible' => true])],
            ['s3'],
            [new State('s4', State::TYPE_NORMAL, [], ['visible' => true])],
            [new State('s5', State::TYPE_FINAL, [], ['visible' => false])],
        ];
    }

    public function transitionsProvider(): array
    {
        return [
            ['t12', 's1', 's2'],
            ['t23', 's2', 's3'],
            ['t34', 's3', 's4'],
            ['t45', 's4', 's5'],
        ];
    }

    protected function addStates(): void
    {
        foreach ($this->statesProvider() as $state) {
            $this->object->addState($state[0]);
        }
    }

    protected function addTransitions(): void
    {
        foreach ($this->transitionsProvider() as $transitions) {
            $this->object->addTransition($transitions[0], $transitions[1], $transitions[2]);
        }
    }

    /**
     * @throws \Finite\Exception\ObjectException
     */
    protected function initialize(): void
    {
        $this->addStates();
        $this->addTransitions();
        $this->object->setObject($this->getStatefulObjectMock());
        $this->object->initialize();
    }

    protected function getStatefulObjectMock()
    {
        $mock = $this->createMock(StatefulInterface::class);
        $this->accessor->expects($this->at(0))->method('getState')->willReturn('s2');

        return $mock;
    }
}
