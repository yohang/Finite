<?php

namespace Finite\Tests\Fixtures;

class AlternativeArticle
{
    private SimpleArticleState          $state            = SimpleArticleState::DRAFT;
    private AlternativeArticleState     $alternativeState = AlternativeArticleState::NEW;
    private readonly \DateTimeInterface $createdAt;

    public function __construct(public readonly string $title)
    {
        $this->createdAt = new \DateTimeImmutable;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getState(): SimpleArticleState
    {
        return $this->state;
    }

    public function setState(SimpleArticleState $state): void
    {
        $this->state = $state;
    }

    public function getAlternativeState(): AlternativeArticleState
    {
        return $this->alternativeState;
    }

    public function setAlternativeState(AlternativeArticleState $alternativeState): void
    {
        $this->alternativeState = $alternativeState;
    }
}
