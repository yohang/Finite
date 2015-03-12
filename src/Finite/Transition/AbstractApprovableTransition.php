<?php
namespace Finite\Transition;

use Finite\StateMachine\StateMachineInterface;
use Finite\Transition\Transition;
use Finite\Transition\ApprovableTransitionInterface;

/**
 * Abstract approvable transition
 *
 * @author Jan Markmann <jan.markmann@preis24.de>
 */
abstract class AbstractApprovableTransition extends Transition implements ApprovableTransitionInterface
{
    abstract public function isApproved(StateMachineInterface $stateMachine);
}
