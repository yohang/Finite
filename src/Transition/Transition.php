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
        public readonly array $properties = []
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

    public function hasProperty(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    public function getPropertyValue(string $name): mixed
    {
        if (!$this->hasProperty($name)) {
            throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $name));
        }

        return $this->properties[$name];
    }

    public function getPropertyNames(): array
    {
        return array_keys($this->properties);
    }
}
