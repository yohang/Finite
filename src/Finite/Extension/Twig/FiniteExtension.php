<?php

namespace Finite\Extension\Twig;

use Finite\Context;
use Finite\StatefulInterface;

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
     * @param StatefulInterface $object
     *
     * @return string
     */
    public function getFiniteState(StatefulInterface $object)
    {
        return $this->context->getState($object);
    }

    /**
     * @param StatefulInterface $object
     *
     * @return array
     */
    public function getFiniteTransitions(StatefulInterface $object)
    {
        return $this->context->getTransitions($object);
    }

    /**
     * @param StatefulInterface $object
     *
     * @return array
     */
    public function getFiniteProperties(StatefulInterface $object)
    {
        return $this->context->getProperties($object);
    }

    /**
     * @param StatefulInterface $object
     * @param string            $property
     *
     * @return bool
     */
    public function hasFiniteProperty(StatefulInterface $object, $property)
    {
        return $this->context->hasProperty($object, $property);
    }

    /**
     * @param StatefulInterface $object
     * @param $transition
     *
     * @return bool|mixed
     */
    public function canFiniteTransition(StatefulInterface $object, $transition)
    {
        return $this->context->getStateMachine($object)->can($transition);
    }

    /**
     * @{inheritDoc}
     */
    public function getName()
    {
        return 'finite';
    }
}
