<?php

namespace Finite\StateMachine;

use Finite\Transition\TransitionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Security Aware state machine.
 * Use the Symfony Security Component and ACL.
 *
 * Need an ACL implementation available, Doctrine DBAL by default.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class SecurityAwareStateMachine extends StateMachine
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function setSecurityContext(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritDoc}
     */
    public function can($transition, array $parameters = array())
    {
        $transition = $transition instanceof TransitionInterface ? $transition : $this->getTransition($transition);

        if (!$this->securityContext->isGranted($transition->getName(), $this->getObject())) {
            return false;
        }

        return parent::can($transition, $parameters);
    }
}
