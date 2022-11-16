<?php

namespace Finite\Tests\E2E;

use Finite\State;
use Finite\Transition\Transition;

enum AlternativeArticleState: string implements State
{
    case NEW  = 'new';
    case READ = 'read';
    case OLD  = 'old';

    const MARK_READ = 'mark_read';
    const MARK_OLD  = 'old';

    public function isFeatured(): bool
    {
        return $this === self::NEW;
    }

    public function isVisible(): bool
    {
        return in_array($this, [self::NEW, self::READ]);
    }

    public static function getTransitions(): array
    {
        return [
            new Transition(self::MARK_READ, [self::NEW], self::READ),
            new Transition(self::MARK_OLD, [self::READ], self::OLD),
        ];
    }
}
