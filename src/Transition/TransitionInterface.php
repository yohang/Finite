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
     * @return State[]
     */
    public function getSourceStates(): array;

    public function getTargetState(): State;

    public function process(object $object): void;

    public function getName(): string;
}
