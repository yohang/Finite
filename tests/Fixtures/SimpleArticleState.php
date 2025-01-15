<?php

declare(strict_types=1);

namespace Finite\Tests\Fixtures;

use Finite\State;
use Finite\Transition\Transition;

enum SimpleArticleState: string implements State
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case REPORTED = 'reported';
    case DISABLED = 'disabled';

    public const PUBLISH = 'publish';
    public const CLEAR = 'clear';
    public const REPORT = 'report';
    public const DISABLE = 'disable';

    public function isVisible(): bool
    {
        return \in_array($this, [self::PUBLISHED, self::REPORTED], true);
    }

    public function isReviewable(): bool
    {
        return \in_array($this, [self::DRAFT, self::REPORTED], true);
    }

    public static function getTransitions(): array
    {
        return [
            new Transition(self::PUBLISH, [self::DRAFT], self::PUBLISHED),
            new Transition(self::CLEAR, [self::REPORTED, self::DISABLED], self::PUBLISHED),
            new Transition(self::REPORT, [self::PUBLISHED], self::REPORTED),
            new Transition(self::DISABLE, [self::REPORTED, self::PUBLISHED], self::DISABLED),
        ];
    }
}
