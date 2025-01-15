<?php

namespace Finite\Dumper;

use Finite\State;

interface Dumper
{
    /**
     * @param enum-string<State> $stateEnum
     */
    public function dump(string $stateEnum): string;
}
