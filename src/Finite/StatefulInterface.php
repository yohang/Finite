<?php

namespace Finite;

/**
 * Implementing this interface make an object Stateful and
 * able to be handled by the state machine.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface StatefulInterface
{
    /**
     * Gets the object state.
     *
     * @return string
     */
    public function getFiniteState();

    /**
     * Sets the object state.
     *
     * @param string $state
     */
    public function setFiniteState($state);
}
