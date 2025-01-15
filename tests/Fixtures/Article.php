<?php

declare(strict_types=1);

namespace Finite\Tests\Fixtures;

class Article
{
    public $noTypeHere;

    public int|float $unionType = 0;

    public \Traversable&\Countable $intersectionType;

    public string $namedType = 'named';

    private SimpleArticleState $state = SimpleArticleState::DRAFT;

    private readonly \DateTimeInterface $createdAt;

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
}
