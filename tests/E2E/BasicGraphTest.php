<?php

namespace Finite\Tests\E2E;

use Finite\StateMachine;
use Finite\Tests\Fixtures\Article;
use Finite\Tests\Fixtures\SimpleArticleState;
use PHPUnit\Framework\TestCase;

class BasicGraphTest extends TestCase
{
    private Article      $article;
    private StateMachine $stateMachine;

    protected function setUp(): void
    {
        $this->article      = new Article('Hi ! I\'m an article.');
        $this->stateMachine = new StateMachine;
    }


    public function test_instantiate_and_have_state(): void
    {

        $this->assertSame('draft', $this->article->getState()->value);
    }

    public function test_it_has_transitions(): void
    {
        $this->assertCount(4, $this->article->getState()::getTransitions());
        $this->assertCount(1, $this->stateMachine->getReachablesTransitions($this->article));

        $this->assertSame(SimpleArticleState::PUBLISHED, $this->stateMachine->getReachablesTransitions($this->article)[0]->getTargetState());
    }

    public function test_it_reject_bad_transitions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->stateMachine->can($this->article, 'touch this');
    }

    public function test_it_allows_to_transition(): void
    {
        $this->assertTrue($this->stateMachine->can($this->article, SimpleArticleState::PUBLISH));
        $this->assertFalse($this->stateMachine->can($this->article, SimpleArticleState::REPORT));
    }

    public function test_it_applies_transition(): void
    {
        $this->stateMachine->apply($this->article, SimpleArticleState::PUBLISH);

        $this->assertSame(SimpleArticleState::PUBLISHED, $this->article->getState());
    }

    public function test_it_reject_bad_transition(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->stateMachine->apply($this->article, SimpleArticleState::REPORT);
    }
}
