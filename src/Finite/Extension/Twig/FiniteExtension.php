<?php

namespace Finite\Extension\Twig;

use Finite\Context;

/**
 * The Finite Twig extension
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
     * @{inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'finite_state'       => new \Twig_Function_Method($this, 'getFiniteState'),
            'finite_transitions' => new \Twig_Function_Method($this, 'getFiniteTransitions'),
            'finite_properties'  => new \Twig_Function_Method($this, 'getFiniteProperties'),
            'finite_has'         => new \Twig_Function_Method($this, 'hasFiniteProperty'),
            'finite_can'         => new \Twig_Function_Method($this, 'canFiniteTransition'),
        );
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
     *
     * @return array
     */
    public function getFiniteTransitions($object, $graph = 'default')
    {
        return $this->context->getTransitions($object, $graph);
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
     * @{inheritDoc}
     */
    public function getName()
    {
        return 'finite';
    }
}
