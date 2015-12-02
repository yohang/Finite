<?php

namespace Finite;

/**
 * Interface that all class that have properties must implements
 *
 * @author
 */
interface PropertiesAwareInterface
{
    /**
     * @param string $property
     *
     * @return bool
     */
    public function has($property);

    /**
     * @param string $property
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($property, $default = null);

    /**
     * Returns optional state properties.
     *
     * @return array
     */
    public function getProperties();
}
