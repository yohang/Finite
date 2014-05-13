<?php

namespace Finite\Event;

use Finite\Event\Callback\Callback;
use Finite\Event\Callback\CallbackSpecification;
use Finite\StateMachine\StateMachineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manage callback-to-event bindings by trigger spec definition
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackHandler
{
    /**
     * @deprecated To be removed in 2.0
     */
    const ALL = 'all';

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var OptionsResolver
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
        $this->specResolver->setAllowedTypes('on', array('string', 'array'));
        $this->specResolver->setAllowedTypes('from', array('string', 'array'));
        $this->specResolver->setAllowedTypes('to', array('string', 'array'));
        $this->specResolver->setAllowedTypes('exclude_from', array('string', 'array'));
        $this->specResolver->setAllowedTypes('exclude_to', array('string', 'array'));

        $toArrayNormalizer = function (Options $options, $value) {
            return (array) $value;
        };

        $this->specResolver->setNormalizer('on', $toArrayNormalizer);
        $this->specResolver->setNormalizer('from', $toArrayNormalizer);
        $this->specResolver->setNormalizer('to', $toArrayNormalizer);
        $this->specResolver->setNormalizer('exclude_to', $toArrayNormalizer);
        $this->specResolver->setNormalizer('exclude_from', $toArrayNormalizer);
    }

    /**
     * @param StateMachineInterface $sm
     * @param callable              $callback
     * @param array                 $spec
     *
     * @return CallbackHandler
     */
    public function addBefore(StateMachineInterface $sm, $callback, array $spec = array())
    {
        $this->add($sm, FiniteEvents::PRE_TRANSITION, $callback, $spec);

        return $this;
    }

    /**
     * @param StateMachineInterface $sm
     * @param callable              $callback
     * @param array                 $spec
     *
     * @return CallbackHandler
     */
    public function addAfter(StateMachineInterface $sm, $callback, array $spec = array())
    {
        $this->add($sm, FiniteEvents::POST_TRANSITION, $callback, $spec);

        return $this;
    }

    /**
     * @param StateMachineInterface $sm
     * @param string                $event
     * @param callable              $callable
     * @param array                 $specs
     *
     * @return CallbackHandler
     */
    protected function add(StateMachineInterface $sm, $event, $callable, array $specs)
    {
        $specs    = $this->specResolver->resolve($specs);
        $callback = new Callback(new CallbackSpecification($sm, $specs['from'], $specs['to'], $specs['on']), $callable);

        $this->dispatcher->addListener($event, $callback);

        return $this;
    }
}
