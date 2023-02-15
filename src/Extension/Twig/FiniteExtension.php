<?php

namespace Finite\Extension\Twig;

use Finite\StateMachine;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FiniteExtension extends AbstractExtension
{
    public function __construct(
        private readonly StateMachine $stateMachine,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'finite_can',
                fn(object $object, string $transitionName, ?string $stateClass = null) => $this->stateMachine->can($object, $transitionName, $stateClass),
            ),
        ];
    }
}
