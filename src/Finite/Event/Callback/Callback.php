<?php

namespace Finite\Event\Callback;

use Finite\Event\TransitionEvent;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class Callback implements CallbackInterface
{
    const CLAUSE_AFTER = 'after';
    const CLAUSE_BEFORE = 'before';
    const CLAUSE_FROM = 'from';
    const CLAUSE_TO = 'to';
    const CLAUSE_ON = 'on';
    const CLAUSE_DO = 'do';

    /**
     * @var CallbackSpecificationInterface
     */
    private $specification;

    /**
     * @var array callable
     */
    private $callable;

    /**
     * @param CallbackSpecificationInterface $callbackSpecification
     * @param $callable
     */
    public function __construct(CallbackSpecificationInterface $callbackSpecification, $callable)
    {
        $this->specification = $callbackSpecification;
        $this->callable = $callable;
    }

    /**
     * @return CallbackSpecificationInterface
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * @return array callable
     */
    public function getCallbacks()
    {
        return $this->callable;
    }

    /**
     * @return array
     */
    public function getClauses()
    {
        return $this->specification->getClauses();
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(TransitionEvent $event)
    {
        if ($this->specification->isSatisfiedBy($event)) {
            $this->call($event->getStateMachine()->getObject(), $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function call($object, TransitionEvent $event)
    {
        return call_user_func($this->callable, $object, $event);
    }
}
