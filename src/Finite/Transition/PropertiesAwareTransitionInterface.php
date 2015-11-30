<?php

namespace Finite\Transition;

/**
 * @author Yohan Giarelli <yohan@giarel.li>
 */
interface PropertiesAwareTransitionInterface
{
    /**
     * Returns an array with resolved properties of transition at the moment
     * it is applied. It's a merge between default properties and "at-apply" properties.
     *
     * @param array $properties
     *
     * @return array
     */
    public function resolveProperties(array $properties);
}
