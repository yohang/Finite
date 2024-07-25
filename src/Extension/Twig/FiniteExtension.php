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
                /** @param class-string|null $stateClass */
                fn(object $object, string $transitionName, ?string $stateClass = null) => $this->stateMachine->can($object, $transitionName, $stateClass),
            ),
            new TwigFunction(
                'finite_reachable_transitions',
                /** @param class-string|null $stateClass */
                fn(object $object, ?string $stateClass = null) => $this->stateMachine->getReachablesTransitions($object, $stateClass),
            ),
        ];
    }
}
