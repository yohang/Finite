<?php

namespace Finite\Event\Callback;

use Finite\Event\CallbackHandler;
use Finite\Event\TransitionEvent;
use Finite\StateMachine\StateMachineInterface;

/**
 * Concrete implementation of CallbackSpecification.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackSpecification implements CallbackSpecificationInterface
{
    /**
     * @var array
     */
    private $specs = array();

    /**
     * @var StateMachineInterface
     */
    private $stateMachine;

    /**
     * @param StateMachineInterface $sm
     * @param array                 $from
     * @param array                 $to
     * @param array                 $on
     */
    public function __construct(StateMachineInterface $sm, array $from, array $to, array $on)
    {
        $this->stateMachine = $sm;

        $isExclusion = function ($str) { return 0 === strpos($str, '-'); };
        $removeDash = function ($str) { return substr($str, 1); };

        foreach (array('from', 'to', 'on') as $clause) {
            $excludedClause = 'excluded_'.$clause;

            $this->specs[$excludedClause] = array_filter(${$clause}, $isExclusion);
            $this->specs[$clause] = array_diff(${$clause}, $this->specs[$excludedClause]);
            $this->specs[$excludedClause] = array_map($removeDash, $this->specs[$excludedClause]);

            // For compatibility with old CallbackHandler.
            // To be removed in 2.0
            if (in_array(CallbackHandler::ALL, $this->specs[$clause])) {
                $this->specs[$clause] = array();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy(TransitionEvent $event)
    {
        return
            $event->getStateMachine() === $this->stateMachine &&
            $this->supportsClause('from', $event->getInitialState()->getName()) &&
            $this->supportsClause('to', $event->getTransition()->getState()) &&
            $this->supportsClause('on', $event->getTransition()->getName());
    }

    /**
     * @param string $clause
     * @param string $property
     *
     * @return bool
     */
    private function supportsClause($clause, $property)
    {
        $excludedClause = 'excluded_'.$clause;

        return
            (0 === count($this->specs[$clause]) || in_array($property, $this->specs[$clause])) &&
            (0 === count($this->specs[$excludedClause]) || !in_array($property, $this->specs[$excludedClause]));
    }
}
