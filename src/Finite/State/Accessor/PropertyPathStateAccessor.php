<?php

namespace Finite\State\Accessor;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Property path implementation of state accessor
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class PropertyPathStateAccessor implements StateAccessorInterface
{
    /**
     * @var string
     */
    private $propertyPath;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param string                    $propertyPath
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct($propertyPath = 'finalState', PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyPath     = $propertyPath;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritDoc}
     */
    public function getState($object)
    {
        return $this->propertyAccessor->getValue($object, $this->propertyPath);
    }

    /**
     * {@inheritDoc}
     */
    public function setState(&$object, $value)
    {
        $this->propertyAccessor->setValue($object, $this->propertyPath, $value);
    }
}
