<?php

namespace Finite\Extension\Twig;

use Finite\StateMachine;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FiniteExtension extends AbstractExtension
{
    public function __construct(
        private readonly StateMachine $stateMachine,
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('finite_can', fn (...$args) => $this->stateMachine->can(...$args)),
        ];
    }
}
