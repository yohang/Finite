<?php

declare(strict_types=1);

namespace Finite\Tests\E2E;

use Finite\StateMachine;
use Finite\Tests\Fixtures\AlternativeArticle;
use Finite\Tests\Fixtures\AlternativeArticleState;
use PHPUnit\Framework\TestCase;

class AlternativeGraphTest extends TestCase
{
    private AlternativeArticle $article;
    private StateMachine $stateMachine;

    protected function setUp(): void
    {
        $this->article = new AlternativeArticle('Hi ! I\'m an article.');
        $this->stateMachine = new StateMachine();
    }

    public function testItHasTransitions(): void
    {
        $this->assertCount(2, $this->article->getAlternativeState()::getTransitions());
        $this->assertCount(1, $this->stateMachine->getReachablesTransitions($this->article, AlternativeArticleState::class));

        $this->assertSame(
            AlternativeArticleState::READ,
            $this->stateMachine->getReachablesTransitions($this->article, AlternativeArticleState::class)[0]->getTargetState(),
        );
    }

    public function testItAllowsToTransition(): void
    {
        $this->assertTrue($this->stateMachine->can($this->article, AlternativeArticleState::MARK_READ, AlternativeArticleState::class));
        $this->assertFalse($this->stateMachine->can($this->article, AlternativeArticleState::MARK_OLD, AlternativeArticleState::class));
    }

    public function testItAppliesTransition(): void
    {
        $this->stateMachine->apply($this->article, AlternativeArticleState::MARK_READ, AlternativeArticleState::class);

        $this->assertSame(AlternativeArticleState::READ, $this->article->getAlternativeState());

        $this->stateMachine->apply($this->article, AlternativeArticleState::MARK_OLD, AlternativeArticleState::class);

        $this->assertSame(AlternativeArticleState::OLD, $this->article->getAlternativeState());
    }

    public function testItRejectBadTransition(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->stateMachine->apply($this->article, AlternativeArticleState::MARK_OLD, AlternativeArticleState::class);
    }
}
