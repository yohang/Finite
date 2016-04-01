<?php

namespace Finite\Loader;

use Finite\Event\Callback\CallbackBuilderFactory;
use Finite\Event\Callback\CallbackBuilderFactoryInterface;
use Finite\Event\CallbackHandler;
use Finite\State\Accessor\PropertyPathStateAccessor;
use Finite\StateMachine\StateMachineInterface;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\Transition\Transition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads a StateMachine from an array.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ArrayLoader implements LoaderInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var CallbackHandler
     */
    private $callbackHandler;

    /**
     * @var CallbackBuilderFactoryInterface
     */
    private $callbackBuilderFactory;

    /**
     * @param array                           $config
     * @param CallbackHandler                 $handler
     * @param CallbackBuilderFactoryInterface $callbackBuilderFactory
     */
    public function __construct(array $config, CallbackHandler $handler = null, CallbackBuilderFactoryInterface $callbackBuilderFactory = null)
    {
        $this->callbackHandler = $handler;
        $this->callbackBuilderFactory = $callbackBuilderFactory;
        $this->config = array_merge(
            array(
                'class' => '',
                'graph' => 'default',
                'property_path' => 'finiteState',
                'states' => array(),
                'transitions' => array(),
            ),
            $config
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load(StateMachineInterface $stateMachine)
    {
        if (null === $this->callbackHandler) {
            $this->callbackHandler = new CallbackHandler($stateMachine->getDispatcher());
        }

        if (null === $this->callbackBuilderFactory) {
            $this->callbackBuilderFactory = new CallbackBuilderFactory();
        }

        if (!$stateMachine->hasStateAccessor()) {
            $stateMachine->setStateAccessor(new PropertyPathStateAccessor($this->config['property_path']));
        }

        $stateMachine->setGraph($this->config['graph']);

        $this->loadStates($stateMachine);
        $this->loadTransitions($stateMachine);
        $this->loadCallbacks($stateMachine);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object, $graph = 'default')
    {
        $reflection = new \ReflectionClass($this->config['class']);

        return $reflection->isInstance($object) && $graph === $this->config['graph'];
    }

    /**
     * @param StateMachineInterface $stateMachine
     */
    private function loadStates(StateMachineInterface $stateMachine)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array('type' => StateInterface::TYPE_NORMAL, 'properties' => array()));
        $resolver->setAllowedValues('type', array(
            StateInterface::TYPE_INITIAL,
            StateInterface::TYPE_NORMAL,
            StateInterface::TYPE_FINAL,
        ));

        foreach ($this->config['states'] as $state => $config) {
            $config = $resolver->resolve($config);
            $stateMachine->addState(new State($state, $config['type'], array(), $config['properties']));
        }
    }

    /**
     * @param StateMachineInterface $stateMachine
     */
    private function loadTransitions(StateMachineInterface $stateMachine)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array('from', 'to'));
        $resolver->setDefaults(array('guard' => null, 'configure_properties' => null, 'properties' => array()));

        $resolver->setAllowedTypes('configure_properties', array('null', 'callable'));

        $resolver->setNormalizer('from', function (Options $options, $v) { return (array) $v; });
        $resolver->setNormalizer('guard', function (Options $options, $v) { return !isset($v) ? null : $v; });
        $resolver->setNormalizer('configure_properties', function (Options $options, $v) {
            $resolver = new OptionsResolver();

            $resolver->setDefaults($options['properties']);

            if (is_callable($v)) {
                $v($resolver);
            }

            return $resolver;
        });

        foreach ($this->config['transitions'] as $transition => $config) {
            $config = $resolver->resolve($config);
            $stateMachine->addTransition(
                new Transition(
                    $transition,
                    $config['from'],
                    $config['to'],
                    $config['guard'],
                    $config['configure_properties']
                )
            );
        }
    }

    /**
     * @param StateMachineInterface $stateMachine
     */
    private function loadCallbacks(StateMachineInterface $stateMachine)
    {
        if (!isset($this->config['callbacks'])) {
            return;
        }

        foreach (array('before', 'after') as $position) {
            $this->loadCallbacksFor($position, $stateMachine);
        }
    }

    private function loadCallbacksFor($position, $stateMachine)
    {
        if (!isset($this->config['callbacks'][$position])) {
            return;
        }

        $method = 'add'.ucfirst($position);
        $resolver = $this->getCallbacksResolver();
        foreach ($this->config['callbacks'][$position] as $specs) {
            $specs = $resolver->resolve($specs);

            $callback = $this->callbackBuilderFactory->createBuilder($stateMachine)
                ->setFrom($specs['from'])
                ->setTo($specs['to'])
                ->setOn($specs['on'])
                ->setCallable($specs['do'])
                ->getCallback();

            $this->callbackHandler->$method($callback);
        }
    }

    private function getCallbacksResolver()
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(
            array(
                'on' => array(),
                'from' => array(),
                'to' => array(),
            )
        );

        $resolver->setRequired(array('do'));

        $resolver->setAllowedTypes('on',   array('string', 'array'));
        $resolver->setAllowedTypes('from', array('string', 'array'));
        $resolver->setAllowedTypes('to',   array('string', 'array'));

        $toArrayNormalizer = function (Options $options, $value) {
            return (array) $value;
        };
        $resolver->setNormalizer('on',  $toArrayNormalizer);
        $resolver->setNormalizer('from', $toArrayNormalizer);
        $resolver->setNormalizer('to',   $toArrayNormalizer);

        return $resolver;
    }
}
