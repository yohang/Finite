<?php

declare(strict_types=1);

namespace Finite\Dumper;

use Finite\State;

interface Dumper
{
    /**
     * @param enum-string<State> $stateEnum
     */
    public function dump(string $stateEnum): string;
}
