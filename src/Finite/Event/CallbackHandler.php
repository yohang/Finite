<?php

namespace Finite\Event;

use Finite\StatefulInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Manage callback-to-event bindings by trigger spec definition
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackHandler
{
    const ALL = 'all';

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var OptionsResolverInterface
     */
    protected $specResolver;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher   = $dispatcher;
        $this->specResolver = new OptionsResolver;
        $this->specResolver->setDefaults(
            array(
                'on'           => self::ALL,
                'from'         => self::ALL,
                'to'           => self::ALL,
                'exclude_from' => array(),
                'exclude_to'   => array(),
            )
        );
        $this->specResolver->setAllowedTypes(
            array(
                'on'           => array('string', 'array'),
                'from'         => array('string', 'array'),
                'to'           => array('string', 'array'),
                'exclude_from' => array('string', 'array'),
                'exclude_to'   => array('string', 'array'),
            )
        );
        $toArrayNormalizer = function($o, $v) { return (array)$v; };
        $this->specResolver->setNormalizers(
            array(
                'on'           => $toArrayNormalizer,
                'from'         => $toArrayNormalizer,
                'to'           => $toArrayNormalizer,
                'exclude_to'   => $toArrayNormalizer,
                'exclude_from' => $toArrayNormalizer,
            )
        );
    }

    /**
     * @param StatefulInterface $object
     * @param callable          $callback
     * @param array             $spec
     *
     * @return CallbackHandler
     */
    public function addBefore(StatefulInterface $object, $callback, array $spec = array())
    {
        $this->add($object, FiniteEvents::PRE_TRANSITION, $callback, $spec);

        return $this;
    }

    /**
     * @param StatefulInterface $object
     * @param string            $event
     * @param callable          $callback
     * @param array             $specs
     *
     * @return CallbackHandler
     */
    protected function add(StatefulInterface $object, $event, $callback, array $specs)
    {
        $specs = $this->processSpecs($specs);
        $listener = function (TransitionEvent $e) use ($object, $callback, $specs) {
            if ($object !== $e->getStateMachine()->getObject()) {
                return;
            }

            if (!(in_array(self::ALL, $specs['to']) || in_array($e->getTransition()->getState(), $specs['to']))) {
                return;
            }

            if (!(in_array(self::ALL, $specs['from']) || in_array($e->getInitialState()->getName(), $specs['from']))) {
                return;
            }

            if (in_array($e->getTransition()->getState(), $specs['exclude_to'])) {
                return;
            }

            if (in_array($e->getInitialState()->getName(), $specs['exclude_from'])) {
                return;
            }

            $callback($e);
        };

        $events = array($event);
        if (count($specs['on']) > 0 && !in_array(self::ALL, $specs['on'])) {
            $events = array_map(function($v) use ($event) { return $event.'.'.$v; }, $specs['on']);
        }

        foreach ($events as $event) {
            $this->dispatcher->addListener($event, $listener);
        }

        return $this;
    }

    /**
     * @param array $specs
     *
     * @return array
     */
    protected function processSpecs(array $specs)
    {
        $specs = $this->specResolver->resolve($specs);
        foreach (array('from', 'to') as $target) {
            foreach ($specs[$target] as $key => $state) {
                if ($state[0] === '-') {
                    $specs['exclude_'.$target][] = substr($state, 1);
                    unset($specs[$target][$key]);
                }
            }

            if (0 === count($specs[$target])) {
                $specs[$target][] = self::ALL;
            }
        }

        return $specs;
    }
}
