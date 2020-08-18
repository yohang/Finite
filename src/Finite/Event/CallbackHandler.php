<?php

namespace Finite\Event;

use Finite\Event\Callback\Callback;
use Finite\Event\Callback\CallbackBuilder;
use Finite\StateMachine\StateMachineInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manage callback-to-event bindings by trigger spec definition.
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
     * @var StateMachineDispatcher
     */
    protected $dispatcher;

    /**
     * @var OptionsResolver
     */
    protected $specResolver;

    /**
     * @param StateMachineDispatcher $dispatcher
     */
    public function __construct(StateMachineDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->specResolver = new OptionsResolver();
        $this->specResolver->setDefaults(
            array(
                'on' => self::ALL,
                'from' => self::ALL,
                'to' => self::ALL,
            )
        );

        $this->specResolver->setAllowedTypes('on', array('string', 'array'));
        $this->specResolver->setAllowedTypes('from', array('string', 'array'));
        $this->specResolver->setAllowedTypes('to', array('string', 'array'));

        $toArrayNormalizer = function (Options $options, $value) {
            return (array) $value;
        };

        $this->specResolver->setNormalizer('on', $toArrayNormalizer);
        $this->specResolver->setNormalizer('from', $toArrayNormalizer);
        $this->specResolver->setNormalizer('to', $toArrayNormalizer);
    }

    /**
     * @param StateMachineInterface|Callback $smOrCallback
     * @param callable                       $callback
     * @param array                          $spec
     *
     * @return CallbackHandler
     */
    public function addBefore($smOrCallback, $callback = null, array $spec = array())
    {
        $this->add($smOrCallback, FiniteEvents::PRE_TRANSITION, $callback, $spec);

        return $this;
    }

    /**
     * @param StateMachineInterface|Callback $smOrCallback
     * @param callable                       $callback
     * @param array                          $spec
     *
     * @return CallbackHandler
     */
    public function addAfter($smOrCallback, $callback = null, array $spec = array())
    {
        $this->add($smOrCallback, FiniteEvents::POST_TRANSITION, $callback, $spec);

        return $this;
    }

    /**
     * @param StateMachineInterface|Callback $smOrCallback
     * @param string                         $event
     * @param callable                       $callable
     * @param array                          $specs
     *
     * @return CallbackHandler
     */
    protected function add($smOrCallback, $event, $callable = null, array $specs = array())
    {
        if ($smOrCallback instanceof Callback) {
            $this->dispatcher->addListener($event, $smOrCallback);

            return $this;
        }

        trigger_error(
            'Use of CallbackHandler::add without a Callback instance is deprecated and will be removed in 2.0',
            E_USER_DEPRECATED
        );

        $specs = $this->specResolver->resolve($specs);
        $callback = CallbackBuilder::create($smOrCallback, $specs['from'], $specs['to'], $specs['on'], $callable)->getCallback();

        $this->dispatcher->addListener($event, $callback);

        return $this;
    }
}
