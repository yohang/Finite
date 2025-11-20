<?php

declare(strict_types=1);

namespace Finite\Tests\Fixtures;

use Finite\State;

enum MockableState: string implements State
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public static function getTransitions(): array
    {
        return MockableTransitionProvider::$transitions;
    }
}
