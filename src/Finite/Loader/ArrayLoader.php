<?php

namespace Finite\Loader;

use Finite\StatefulInterface;
use Finite\StateMachine\StateMachineInterface;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\Transition\Transition;
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
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge(
            array(
                'class'       => '',
                'states'      => array(),
                'transitions' => array(),
            ),
            $config
        );
    }

    /**
     * @{inheritDoc}
     */
    public function load(StateMachineInterface $stateMachine)
    {
        $this->loadStates($stateMachine);
        $this->loadTransitions($stateMachine);
    }

    /**
     * @{inheritDoc}
     */
    public function supports(StatefulInterface $object)
    {
        $reflection = new \ReflectionClass($this->config['class']);

        return $reflection->isInstance($object);
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
        $resolver->setNormalizers(array('from' => function($v) { return (array)$v; }));

        foreach ($this->config['transitions'] as $transition => $config) {
            $config = $resolver->resolve($config);
            $stateMachine->addTransition(new Transition($transition, $config['from'], $config['to']));
        }
    }
}
