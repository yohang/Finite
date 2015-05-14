<?php

namespace Finite\Loader;

use Finite\Event\CallbackHandler;
use Finite\State\Accessor\PropertyPathStateAccessor;
use Finite\StateMachine\StateMachineInterface;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\Transition\Transition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads a StateMachine from an array
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
     * @param array           $config
     * @param CallbackHandler $handler
     */
    public function __construct(array $config, CallbackHandler $handler = null)
    {
        $this->callbackHandler = $handler;
        $this->config = array_merge(
            array(
                'class'         => '',
                'graph'         => 'default',
                'property_path' => 'finiteState',
                'states'        => array(),
                'transitions'   => array(),
            ),
            $config
        );
    }

    /**
     * @{inheritDoc}
     */
    public function load(StateMachineInterface $stateMachine)
    {
        //if (null === $this->callbackHandler) {
            $this->callbackHandler = new CallbackHandler($stateMachine->getDispatcher());
        //}

        $stateMachine->setStateAccessor(new PropertyPathStateAccessor($this->config['property_path']));
        $stateMachine->setGraph($this->config['graph']);

        $this->loadStates($stateMachine);
        $this->loadTransitions($stateMachine);
        $this->loadCallbacks($stateMachine);
    }

    /**
     * @{inheritDoc}
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
        $resolver = new OptionsResolver;
        $resolver->setDefaults(array('type' => StateInterface::TYPE_NORMAL, 'properties' => array()));
        $resolver->setAllowedValues(
            array(
                'type' => array(
                    StateInterface::TYPE_INITIAL,
                    StateInterface::TYPE_NORMAL,
                    StateInterface::TYPE_FINAL
                )
            )
        );

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
        $resolver = new OptionsResolver;
        $resolver->setRequired(array('from', 'to'));
        $resolver->setDefaults(array('guard' => null));
        $resolver->setNormalizers(array(
            'from' => function (Options $options, $v) { return (array) $v; },
            'guard' => function (Options $options, $v) { return !isset($v) ? null : $v; }
        ));

        foreach ($this->config['transitions'] as $transition => $config) {
            $config = $resolver->resolve($config);
            $stateMachine->addTransition(new Transition($transition, $config['from'], $config['to'], $config['guard']));
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
        foreach ($this->config['callbacks'][$position] as $specs) {
            $callback = $specs['do'];
            unset($specs['do']);

            $this->callbackHandler->$method($stateMachine, $callback, $specs);
        }
    }
}
