<?php
namespace Finite\Transition;

use Finite\StateMachine\StateMachine;
use Finite\Transition\TransitionInterface;

/**
 * Approvable transition interface
 *
 * @author Jan Markmann <jan.markmann@preis24.de>
 */
interface ApprovableTransitionInterface extends TransitionInterface
{
    /**
     * @param StateMachine $stateMachine
     *
     * @return bool True if transition is approved
     */
    public function isApproved(StateMachine $stateMachine);
}
