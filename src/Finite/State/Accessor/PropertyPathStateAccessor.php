<?php

namespace Finite\State\Accessor;

use Finite\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException as SymfonyNoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Property path implementation of state accessor.
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
    public function __construct($propertyPath = 'finiteState', PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyPath = $propertyPath;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getState($object)
    {
        try {
            return $this->propertyAccessor->getValue($object, $this->propertyPath);
        } catch (SymfonyNoSuchPropertyException $e) {
            throw new NoSuchPropertyException(sprintf(
                'Property path "%s" on object "%s" does not exist.',
                $this->propertyPath,
                get_class($object)
            ), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setState(&$object, $value)
    {
        try {
            $this->propertyAccessor->setValue($object, $this->propertyPath, $value);
        } catch (SymfonyNoSuchPropertyException $e) {
            throw new NoSuchPropertyException(sprintf(
                'Property path "%s" on object "%s" does not exist.',
                $this->propertyPath,
                get_class($object)
            ), $e->getCode(), $e);
        }
    }
}
