<?php

namespace Finite\Event;

use Finite\StateMachine\StateMachineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\OptionsResolver\Options;
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
                'args'         => array('object', 'event')
            )
        );
        $this->specResolver->setAllowedTypes(
            array(
                'on'           => array('string', 'array'),
                'from'         => array('string', 'array'),
                'to'           => array('string', 'array'),
                'exclude_from' => array('string', 'array'),
                'exclude_to'   => array('string', 'array'),
                'args'         => array('array')
            )
        );
        $toArrayNormalizer = function (Options $options, $value) {
            return (array) $value;
        };
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
     * @param callable              $callback
     * @param array                 $specs
     *
     * @return $this
     */
    protected function add(StateMachineInterface $sm, $event, $callback, array $specs)
    {
        $specs    = $this->processSpecs($specs);
        $listener = function (TransitionEvent $e) use ($sm, $callback, $specs) {
            if ($sm !== $e->getStateMachine()) {
                return;
            }

            if (!(
                in_array(CallbackHandler::ALL, $specs['to']) ||
                in_array($e->getTransition()->getState(), $specs['to'])
            )
            ) {
                return;
            }

            if (!(
                in_array(CallbackHandler::ALL, $specs['from']) ||
                in_array($e->getInitialState()->getName(), $specs['from'])
            )
            ) {
                return;
            }

            if (in_array($e->getTransition()->getState(), $specs['exclude_to'])) {
                return;
            }

            if (in_array($e->getInitialState()->getName(), $specs['exclude_from'])) {
                return;
            }

            $expr = new ExpressionLanguage();

            call_user_func_array($callback, array_map(
                function($arg) use($expr, $e) {
                    return $expr->evaluate($arg, array(
                        'object' => $e->getStateMachine()->getObject(),
                        'event'  => $e
                    ));
                }, $specs['args']
            ));
        };

        $events = array($event);
        if (count($specs['on']) > 0 && !in_array(self::ALL, $specs['on'])) {
            $events = array_map(function ($v) use ($event) {
                return $event . '.' . $v;
            }, $specs['on']);
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
                    $specs['exclude_' . $target][] = substr($state, 1);
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
