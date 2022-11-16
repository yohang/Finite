<?php

namespace Finite;

use Finite\Transition\TransitionInterface;

interface State
{
    /**
     * @return TransitionInterface[]
     */
    public static function getTransitions(): array;
}

