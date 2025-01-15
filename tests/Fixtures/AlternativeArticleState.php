<?php

declare(strict_types=1);

namespace Finite\Tests\Fixtures;

use Finite\State;
use Finite\Transition\Transition;

enum AlternativeArticleState: string implements State
{
    case NEW = 'new';
    case READ = 'read';
    case OLD = 'old';

    public const MARK_READ = 'mark_read';
    public const MARK_OLD = 'old';

    public function isFeatured(): bool
    {
        return self::NEW === $this;
    }

    public function isVisible(): bool
    {
        return \in_array($this, [self::NEW, self::READ], true);
    }

    public static function getTransitions(): array
    {
        return [
            new Transition(self::MARK_READ, [self::NEW], self::READ),
            new Transition(self::MARK_OLD, [self::READ], self::OLD),
        ];
    }
}
