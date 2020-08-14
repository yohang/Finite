<?php

namespace Finite\Extension\Twig;

use Finite\Context;
use Twig\TwigFunction;

if (!class_exists('Twig_Extension')) {
    class_alias('Twig\Extension\AbstractExtension', 'Twig_Extension');
}

/**
 * The Finite Twig extension.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class FiniteExtension extends \Twig_Extension
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        if (class_exists('Twig_SimpleFunction')) {
            return [
                new \Twig_SimpleFunction('finite_state', [$this, 'getFiniteState']),
                new \Twig_SimpleFunction('finite_transitions', [$this, 'getFiniteTransitions']),
                new \Twig_SimpleFunction('finite_properties', [$this, 'getFiniteProperties']),
                new \Twig_SimpleFunction('finite_has', [$this, 'hasFiniteProperty']),
                new \Twig_SimpleFunction('finite_can', [$this, 'canFiniteTransition']),
            ];
        }

        return [
            new TwigFunction('finite_state', [$this, 'getFiniteState']),
            new TwigFunction('finite_transitions', [$this, 'getFiniteTransitions']),
            new TwigFunction('finite_properties', [$this, 'getFiniteProperties']),
            new TwigFunction('finite_has', [$this, 'hasFiniteProperty']),
            new TwigFunction('finite_can', [$this, 'canFiniteTransition']),
        ];
    }

    /**
     * @param object $object
     * @param string $graph
     *
     * @return string
     */
    public function getFiniteState($object, $graph = 'default')
    {
        return $this->context->getState($object, $graph);
    }

    /**
     * @param object $object
     * @param string $graph
     * @param bool   $as_object
     *
     * @return array
     */
    public function getFiniteTransitions($object, $graph = 'default', $as_object = false)
    {
        return $this->context->getTransitions($object, $graph, $as_object);
    }

    /**
     * @param object $object
     * @param string $graph
     *
     * @return array
     */
    public function getFiniteProperties($object, $graph = 'default')
    {
        return $this->context->getProperties($object, $graph);
    }

    /**
     * @param object $object
     * @param string $property
     * @param string $graph
     *
     * @return bool
     */
    public function hasFiniteProperty($object, $property, $graph = 'default')
    {
        return $this->context->hasProperty($object, $property, $graph);
    }

    /**
     * @param object $object
     * @param string $transition
     * @param string $graph
     *
     * @return bool|mixed
     */
    public function canFiniteTransition($object, $transition, $graph = 'default')
    {
        return $this->context->getStateMachine($object, $graph)->can($transition);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'finite';
    }
}
