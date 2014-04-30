<?php

namespace Finite\Callback;

use Finite\Event\TransitionEvent;
use Finite\Factory\FactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Add the ability to cascade a transition to a different graph or different object via a simple callback
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CascadeTransitionCallback
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Apply a transition to the object that has just undergone a transition
     *
     * @param Object          $object     Current object
     * @param TransitionEvent $event      Transition event
     * @param string|null     $transition Transition that is to be applied (if null, same as the trigger)
     * @param string|null     $graph      Graph on which the new transition will apply (if null, same as the trigger)
     */
    public function apply($object, TransitionEvent $event, $transition = null, $graph = null)
    {
        if (null === $transition) {
            $transition = $event->getTransition()->getName();
        }

        if (null === $graph) {
            $graph = $event->getStateMachine()->getGraph();
        }

        $this->factory->get($object, $graph)->apply($transition);
    }
}
