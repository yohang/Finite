<?php

namespace Finite\Tests;

use Finite\Event\CanTransitionEvent;
use Finite\Event\EventDispatcher;
use Finite\Event\PostTransitionEvent;
use Finite\Event\PreTransitionEvent;
use Finite\StateMachine;
use Finite\Tests\E2E\Article;
use Finite\Tests\E2E\SimpleArticleState;
use PHPUnit\Framework\TestCase;

class StateMachineTest extends TestCase
{
    public function test_it_can_transition(): void
    {
        $object = new Article('Hi !');

        $eventDispatcher = $this->getMockBuilder(EventDispatcher::class)->getMock();
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function (CanTransitionEvent $e) use ($object) {
                    $this->assertSame($object, $e->getObject());
                    $this->assertFalse($e->isPropagationStopped());

                    return null === $e->getStateClass();
                }),
            );


        $stateMachine = new StateMachine($eventDispatcher);

        $this->assertTrue($stateMachine->can($object, SimpleArticleState::PUBLISH));
    }

    public function test_it_blocks_transition(): void
    {
        $object = new Article('Hi !');

        $eventDispatcher = $this->getMockBuilder(EventDispatcher::class)->getMock();
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CanTransitionEvent::class))
            ->willReturnCallback(fn (CanTransitionEvent $event) => $event->blockTransition());


        $stateMachine = new StateMachine($eventDispatcher);

        $this->assertFalse($stateMachine->can($object, SimpleArticleState::PUBLISH));
    }

    public function test_it_applies_transition(): void
    {
        $object = new Article('Hi !');

        $eventDispatcher = $this->getMockBuilder(EventDispatcher::class)->getMock();
        $eventDispatcher
            ->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with();

        $eventDispatcher
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                [$this->callback(fn (CanTransitionEvent $e) => SimpleArticleState::PUBLISH === $e->getTransitionName())],
                [$this->callback(fn (PreTransitionEvent $e) => SimpleArticleState::PUBLISH === $e->getTransitionName())],
                [$this->callback(fn (PostTransitionEvent $e) => SimpleArticleState::PUBLISH === $e->getTransitionName())],
            );

        $stateMachine = new StateMachine($eventDispatcher);

        $stateMachine->apply($object, SimpleArticleState::PUBLISH);
    }

    public function test_it_rejects_non_stateful_object()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new StateMachine)->can(
            new class {
                public string $title = 'Foobar';
            },
            'transition',
        );
    }

    public function test_it_rejects_unexistant_state_class()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new StateMachine)->can(new \stdClass, 'transition', 'Unexistant enum');
    }
}
