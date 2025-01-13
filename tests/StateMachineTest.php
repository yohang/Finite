<?php

namespace Finite\Tests;

use Finite\Event\CanTransitionEvent;
use Finite\Event\EventDispatcher;
use Finite\Event\TransitionEvent;
use Finite\StateMachine;
use Finite\Tests\E2E\Article;
use Finite\Tests\E2E\SimpleArticleState;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class StateMachineTest extends TestCase
{
    public function test_it_instantiate_event_dispatcher(): void
    {
        $this->assertInstanceOf(EventDispatcher::class, (new StateMachine)->getDispatcher());
        $this->assertInstanceOf(EventDispatcherInterface::class, (new StateMachine)->getDispatcher());
    }

    public function test_it_use_constructor_event_dispatcher(): void
    {
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $this->assertSame($eventDispatcher, (new StateMachine($eventDispatcher))->getDispatcher());
    }

    public function test_it_can_transition(): void
    {
        $object = new Article('Hi !');

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
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

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CanTransitionEvent::class))
            ->willReturnCallback(fn(CanTransitionEvent $event) => $event->blockTransition());


        $stateMachine = new StateMachine($eventDispatcher);

        $this->assertFalse($stateMachine->can($object, SimpleArticleState::PUBLISH));
    }

    public function test_it_applies_transition(): void
    {
        $object = new Article('Hi !');

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $matcher = $this->exactly(6);
        $eventDispatcher
            ->expects($matcher)
            ->method('dispatch')
            ->willReturnCallback(
                fn(TransitionEvent $e) => match ($matcher->numberOfInvocations()) {
                    1, 2, 3 => SimpleArticleState::PUBLISH === $e->getTransitionName(),
                    4, 5, 6 => SimpleArticleState::REPORT === $e->getTransitionName(),
                }
            );

        $stateMachine = new StateMachine($eventDispatcher);

        $stateMachine->apply($object, SimpleArticleState::PUBLISH);
        $stateMachine->apply($object, SimpleArticleState::REPORT);
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

    public function test_it_throws_if_no_state(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $stateMachine = new StateMachine;
        $stateMachine->can(new class extends \stdClass {}, 'transition');
    }
}
