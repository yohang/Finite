<?php

declare(strict_types=1);

namespace Finite;

use Finite\Transition\TransitionInterface;

interface State
{
    /**
     * @return TransitionInterface[]
     */
    public static function getTransitions(): array;
}
