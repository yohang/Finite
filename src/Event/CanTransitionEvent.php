<?php

declare(strict_types=1);

namespace Finite\Event;

final class CanTransitionEvent extends TransitionEvent
{
    private bool $transitionBlocked = false;

    public function isTransitionBlocked(): bool
    {
        return $this->transitionBlocked;
    }

    public function blockTransition(): void
    {
        $this->transitionBlocked = true;

        $this->stopPropagation();
    }
}
