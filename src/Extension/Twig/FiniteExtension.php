<?php

declare(strict_types=1);

namespace Finite\Extension\Twig;

use Finite\StateMachine;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FiniteExtension extends AbstractExtension
{
    public function __construct(
        private readonly StateMachine $stateMachine,
    ) {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('finite_can', $this->can(...)),
            new TwigFunction('finite_reachable_transitions', $this->finiteReachableTransitions(...)),
        ];
    }

    /**
     * @param class-string|null $stateClass
     */
    public function can(object $object, string $transitionName, ?string $stateClass = null): bool
    {
        return $this->stateMachine->can($object, $transitionName, $stateClass);
    }

    /**
     * @param class-string|null $stateClass
     */
    public function finiteReachableTransitions(object $object, ?string $stateClass = null): array
    {
        return $this->stateMachine->getReachablesTransitions($object, $stateClass);
    }
}
