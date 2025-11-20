<?php

declare(strict_types=1);

namespace Finite\Tests\Fixtures;

use Finite\Transition\Transition;

final class MockableTransitionProvider
{
    /**
     * @var array<int, Transition>
     */
    public static array $transitions = [];
}
