<?php

declare(strict_types=1);

namespace Finite\Tests\Fixtures;

class AlternativeArticle
{
    private readonly \DateTimeInterface $createdAt;
    private SimpleArticleState $state = SimpleArticleState::DRAFT;
    private AlternativeArticleState $alternativeState = AlternativeArticleState::NEW;

    public function __construct(public readonly string $title)
    {
        $this->createdAt = new \DateTimeImmutable();
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
