<?php

namespace Finite\Visualisation;

use Finite\StateMachine\StateMachineInterface;

/**
 * Interface for state machine visualisation strategies.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
interface VisualisationInterface
{
    public function render(StateMachineInterface $stateMachine);
}
