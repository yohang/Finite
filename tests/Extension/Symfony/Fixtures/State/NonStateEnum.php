<?php

declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony\Fixtures\State;

enum NonStateEnum: string
{
    case NOT_A_STATE = 'not_a_state';
}
