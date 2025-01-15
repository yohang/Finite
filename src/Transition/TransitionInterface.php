<?php

namespace Finite\Transition;

use Finite\State;

/**
 * The base Transition interface.
 *
 * @author Yohan Giarelli <yohan@giarel.li>
 */
interface TransitionInterface
{
    /**
     * @return array<int, \BackedEnum&State>
     */
    public function getSourceStates(): array;

    public function getTargetState(): State&\BackedEnum;

    public function process(object $object): void;

    public function getName(): string;

    public function hasProperty(string $name): bool;

    public function getPropertyValue(string $name): mixed;

    /**
     * @return array<int, string>
     */
    public function getPropertyNames(): array;
}
