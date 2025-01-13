<?php
declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony\Fixtures\State;

use Finite\State;
use Finite\Transition\Transition;

enum DocumentState: string implements State
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case REPORTED = 'reported';
    case DISABLED = 'disabled';

    public static function getTransitions(): array
    {
        return [
            new Transition('publish', [self::DRAFT], self::PUBLISHED),
            new Transition('clear', [self::REPORTED, self::DISABLED], self::PUBLISHED),
            new Transition('report', [self::PUBLISHED], self::REPORTED),
            new Transition('disable', [self::REPORTED, self::PUBLISHED], self::DISABLED),
        ];
    }
}
