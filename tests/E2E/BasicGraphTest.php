<?php

declare(strict_types=1);

namespace Finite\Tests\E2E;

use Finite\Exception\FiniteException;
use Finite\Exception\TransitionNotReachableException;
use Finite\StateMachine;
use Finite\Tests\Fixtures\Article;
use Finite\Tests\Fixtures\SimpleArticleState;
use PHPUnit\Framework\TestCase;

class BasicGraphTest extends TestCase
{
    private Article $article;
    private StateMachine $stateMachine;

    protected function setUp(): void
    {
        $this->article = new Article('Hi ! I\'m an article.');
        $this->stateMachine = new StateMachine();
    }

    public function testInstantiateAndHaveState(): void
    {
        $this->assertSame('draft', $this->article->getState()->value);
    }

    public function testItHasTransitions(): void
    {
        $this->assertCount(4, $this->article->getState()::getTransitions());
        $this->assertCount(1, $this->stateMachine->getReachablesTransitions($this->article));

        $this->assertSame(SimpleArticleState::PUBLISHED, $this->stateMachine->getReachablesTransitions($this->article)[0]->getTargetState());
    }

    public function testItRejectBadTransitions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectException(FiniteException::class);
        $this->expectException(TransitionNotReachableException::class);

        $this->stateMachine->can($this->article, 'touch this');
    }

    public function testItAllowsToTransition(): void
    {
        $this->assertTrue($this->stateMachine->can($this->article, SimpleArticleState::PUBLISH));
        $this->assertFalse($this->stateMachine->can($this->article, SimpleArticleState::REPORT));
    }

    public function testItAppliesTransition(): void
    {
        $this->stateMachine->apply($this->article, SimpleArticleState::PUBLISH);

        $this->assertSame(SimpleArticleState::PUBLISHED, $this->article->getState());
    }

    public function testItRejectBadTransition(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->stateMachine->apply($this->article, SimpleArticleState::REPORT);
    }
}
