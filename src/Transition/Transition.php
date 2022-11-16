<?php

namespace Finite\Transition;

use Finite\State;

/**
 * The base Transition class.
 * Feel free to extend it to fit to your needs.
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
class Transition implements TransitionInterface
{
    public function __construct(
        public readonly string $name,
        public readonly array $sourceStates,
        public readonly State $targetState,
    )
    {
    }

    public function getSourceStates(): array
    {
        return $this->sourceStates;
    }

    public function getTargetState(): State
    {
        return $this->targetState;
    }

    public function process(object $object): void
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
