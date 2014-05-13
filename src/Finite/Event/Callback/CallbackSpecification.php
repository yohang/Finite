<?php

namespace Finite\Event\Callback;

use Finite\Event\TransitionEvent;

/**
 * Concrete implementation of CallbackSpecification
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
     * @var callable
     */
    private $callback;

    /**
     * @param array    $from
     * @param array    $to
     * @param array    $on
     * @param callable $callback
     */
    public function __construct(array $from, array $to, array $on, $callback)
    {
        $isExclusion = function ($str) {
            return 0 === strpos($str, '-');
        };
        $removeDash  = function ($str) {
            return substr($str, 1);
        };

        foreach (array('from', 'to', 'on') as $clause) {
            $excludedClause = 'excluded_' . $clause;

            $this->specs[$excludedClause] = array_filter(${$clause}, $isExclusion);
            $this->specs[$clause]         = array_diff(${$clause}, $this->specs[$excludedClause]);
            $this->specs[$excludedClause] = array_map($removeDash, $this->specs[$excludedClause]);
        }

        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(TransitionEvent $event)
    {
        return
            $this->supportClause('from', $event->getInitialState()) &&
            $this->supportClause('to', $event->getTransition()->getState()) &&
            $this->supportClause('on', $event->getTransition()->getName());
    }

    /**
     * @param string $clause
     * @param string $property
     *
     * @return bool
     */
    private function supportClause($clause, $property)
    {
        $excludedClause = 'excluded_' . $clause;

        return
            (0 === count($this->specs[$clause]) || in_array($property, $this->specs[$clause])) &&
            (0 === count($this->specs[$excludedClause]) || !in_array($property, $this->specs[$excludedClause]));
    }
}
