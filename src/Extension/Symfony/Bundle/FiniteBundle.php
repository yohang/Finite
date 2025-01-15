<?php

declare(strict_types=1);

namespace Finite\Extension\Symfony\Bundle;

use Finite\Extension\Symfony\Command\DumpStateMachineCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class FiniteBundle extends Bundle
{
    public function registerCommands(Application $application): void
    {
        $application->add(new DumpStateMachineCommand());
    }
}
