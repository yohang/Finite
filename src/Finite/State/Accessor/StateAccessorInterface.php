<?php

namespace Finite\State\Accessor;

/**
 * Base interface for state accessors
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface StateAccessorInterface
{
    /**
     * Retrieves the current state from the given object
     *
     * @param object $object
     *
     * @return string
     */
    public function getState($object);

    /**
     * Set the state of the object to the given property path
     *
     * @param object $object
     * @param string $value
     */
    public function setState(&$object, $value);
}
