<?php

declare(strict_types=1);

namespace Finite\Dumper;

use Finite\State;

class MermaidDumper implements Dumper
{
    /**
     * @param enum-string<State> $stateEnum
     */
    public function dump(string $stateEnum): string
    {
        $output = [
            '---',
            'title: '.$stateEnum,
            '---',
            'stateDiagram-v2',
        ];

        foreach ($stateEnum::getTransitions() as $transition) {
            foreach ($transition->getSourceStates() as $state) {
                $output[] = \sprintf(
                    '    %s --> %s: %s',
                    $state->value,
                    $transition->getTargetState()->value,
                    $transition->getName()
                );
            }
        }

        return implode(\PHP_EOL, $output);
    }
}
