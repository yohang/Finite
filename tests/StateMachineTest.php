<?php

declare(strict_types=1);

namespace Finite\Tests;

use Finite\Event\CanTransitionEvent;
use Finite\Event\EventDispatcher;
use Finite\Event\TransitionEvent;
use Finite\Exception\BadStateClassException;
use Finite\Exception\FiniteException;
use Finite\Exception\NonUniqueStateException;
use Finite\Exception\NoStateFoundException;
use Finite\StateMachine;
use Finite\Tests\Fixtures\AlternativeArticle;
use Finite\Tests\Fixtures\AlternativeArticleState;
use Finite\Tests\Fixtures\Article;
use Finite\Tests\Fixtures\SimpleArticleState;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class StateMachineTest extends TestCase
{
    public function testItInstantiateEventDispatcher(): void
    {
        $this->assertInstanceOf(EventDispatcher::class, (new StateMachine())->getDispatcher());
        $this->assertInstanceOf(EventDispatcherInterface::class, (new StateMachine())->getDispatcher());
    }

    public function testItUseConstructorEventDispatcher(): void
    {
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $this->assertSame($eventDispatcher, (new StateMachine($eventDispatcher))->getDispatcher());
    }

    public function testItCanTransition(): void
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
                    $this->assertSame(SimpleArticleState::DRAFT, $e->getFromState());

                    return SimpleArticleState::class === $e->getStateClass();
                }),
            );

        $stateMachine = new StateMachine($eventDispatcher);

        $this->assertTrue($stateMachine->can($object, SimpleArticleState::PUBLISH));
    }

    public function testItBlocksTransition(): void
    {
        $object = new Article('Hi !');

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CanTransitionEvent::class))
            ->willReturnCallback(fn (CanTransitionEvent $event) => $event->blockTransition());

        $stateMachine = new StateMachine($eventDispatcher);

        $this->assertFalse($stateMachine->can($object, SimpleArticleState::PUBLISH));
    }

    public function testItAppliesTransition(): void
    {
        $object = new Article('Hi !');

        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $matcher = $this->exactly(6);
        $eventDispatcher
            ->expects($matcher)
            ->method('dispatch')
            ->willReturnCallback(
                fn (TransitionEvent $e) => match ($matcher->numberOfInvocations()) {
                    1, 2, 3 => SimpleArticleState::PUBLISH === $e->getTransition()->getName(),
                    4, 5, 6 => SimpleArticleState::REPORT === $e->getTransition()->getName(),
                }
            );

        $stateMachine = new StateMachine($eventDispatcher);

        $stateMachine->apply($object, SimpleArticleState::PUBLISH);
        $stateMachine->apply($object, SimpleArticleState::REPORT);
    }

    public function testItRejectsNonStatefulObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectException(FiniteException::class);
        $this->expectException(NoStateFoundException::class);

        (new StateMachine())->can(
            new class {
                public string $title = 'Foobar';
            },
            'transition',
        );
    }

    public function testItRejectsUnexistantStateClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectException(FiniteException::class);
        $this->expectException(NoStateFoundException::class);

        (new StateMachine())->can(new \stdClass(), 'transition', 'Unexistant enum');
    }

    public function testItThrowsIfNoState(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectException(FiniteException::class);
        $this->expectException(NoStateFoundException::class);

        $stateMachine = new StateMachine();
        $stateMachine->can(new class extends \stdClass {
        }, 'transition');
    }

    public function testItThrowsIfBadState(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectException(FiniteException::class);
        $this->expectException(BadStateClassException::class);

        $stateMachine = new StateMachine();
        $stateMachine->can(new Article('test'), 'publish', AlternativeArticleState::class);
    }

    public function testItThrowsIfManyStateAndNoneGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectException(FiniteException::class);
        $this->expectException(NonUniqueStateException::class);

        $stateMachine = new StateMachine();
        $stateMachine->can(new AlternativeArticle('test'), 'publish');
    }

    public function testItReturnsClassStateClasses(): void
    {
        $this->assertSame(
            [SimpleArticleState::class],
            (new StateMachine())->getStateClasses(new Article('Hi !')),
        );
        $this->assertSame(
            [SimpleArticleState::class, AlternativeArticleState::class],
            (new StateMachine())->getStateClasses(new AlternativeArticle('Hi !')),
        );
        $this->assertSame(
            [],
            (new StateMachine())->getStateClasses(new \stdClass()),
        );
    }

    public function testItReturnsIfObjectHasState(): void
    {
        $this->assertTrue((new StateMachine())->hasState(new Article('Hi !')));
        $this->assertTrue((new StateMachine())->hasState(new AlternativeArticle('Hi !')));
        $this->assertFalse((new StateMachine())->hasState(new \stdClass()));
    }
}
