<?php

namespace Finite\Loader;

use Finite\Event\Callback\Callback;
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

        $stateMachine->setStateAccessor(new PropertyPathStateAccessor($this->config['property_path']));
        $stateMachine->setGraph($this->config['graph']);

        $this->loadCallbacks($stateMachine);
        $this->loadStates($stateMachine);
        $this->loadTransitions($stateMachine);
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
     * @param $triggerName
     *
     * @return array
     */
    protected function findCallbacksByTrigger($triggerName)
    {
        $callbacks = [];

        foreach ([Callback::CLAUSE_BEFORE, Callback::CLAUSE_AFTER] as $position) {
            $callbacks[$position] = [];

            $callbacks[$position] = array_merge(
                $callbacks[$position],
                $this->findCallbacksByTriggerAndPosition($triggerName, $position)
            );
        }

        return $callbacks;
    }

    /**
     * @param $triggerName
     * @param $position
     *
     * @return array
     */
    protected function findCallbacksByTriggerAndPosition($triggerName, $position)
    {
        $callbacks = [];

        if (empty($this->config['callbacks'][$position])) {
            return $callbacks;
        }

        foreach ($this->config['callbacks'][$position] as $callbackName => $callback) {
            foreach ([Callback::CLAUSE_FROM, Callback::CLAUSE_TO, Callback::CLAUSE_ON] as $clause) {
                if (!empty($callback[$clause])) {
                    if ((is_string($callback[$clause]) && $callback[$clause] === $triggerName)
                        || (is_array($callback[$clause]) && in_array($triggerName, $callback[$clause]))) {
                        $callbacks[] = [$callbackName => $callback];
                    }
                }
            }
        }

        return $callbacks;
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
            $stateMachine->addState(new State(
                $state,
                $config['type'],
                [],
                $config['properties'],
                $this->findCallbacksByTrigger($state)
            ));
        }
    }

    /**
     * @param StateMachineInterface $stateMachine
     */
    private function loadTransitions(StateMachineInterface $stateMachine)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(Callback::CLAUSE_FROM, Callback::CLAUSE_TO));
        $resolver->setDefaults(array('guard' => null, 'configure_properties' => null, 'properties' => array()));

        $resolver->setAllowedTypes('configure_properties', array('null', 'callable'));

        $resolver->setNormalizer(Callback::CLAUSE_FROM, function (Options $options, $v) { return (array) $v; });
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
                    $config[Callback::CLAUSE_FROM],
                    $config['to'],
                    $config['guard'],
                    $config['configure_properties'],
                    $this->findCallbacksByTrigger($transition)
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

        $stateMachine->setCallbacks($this->config['callbacks']);

        foreach (array(Callback::CLAUSE_BEFORE, Callback::CLAUSE_AFTER) as $position) {
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
                ->setFrom($specs[Callback::CLAUSE_FROM])
                ->setTo($specs[Callback::CLAUSE_TO])
                ->setOn($specs[Callback::CLAUSE_ON])
                ->setCallable($specs[Callback::CLAUSE_DO])
                ->getCallback();

            $this->callbackHandler->$method($callback);
        }
    }

    private function getCallbacksResolver()
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(
            array(
                Callback::CLAUSE_ON => array(),
                Callback::CLAUSE_FROM => array(),
                Callback::CLAUSE_TO => array(),
            )
        );

        $resolver->setRequired(array(Callback::CLAUSE_DO));

        $resolver->setAllowedTypes(Callback::CLAUSE_ON,   array('string', 'array'));
        $resolver->setAllowedTypes(Callback::CLAUSE_FROM, array('string', 'array'));
        $resolver->setAllowedTypes(Callback::CLAUSE_TO,   array('string', 'array'));

        $toArrayNormalizer = function (Options $options, $value) {
            return (array) $value;
        };
        $resolver->setNormalizer(Callback::CLAUSE_ON,  $toArrayNormalizer);
        $resolver->setNormalizer(Callback::CLAUSE_FROM, $toArrayNormalizer);
        $resolver->setNormalizer(Callback::CLAUSE_TO,   $toArrayNormalizer);

        return $resolver;
    }
}
