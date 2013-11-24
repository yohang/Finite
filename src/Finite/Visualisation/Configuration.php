<?php

namespace Finite\Visualisation;

/**
 * Configuration value object.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Configuration
{
    private $renderProperties;
    private $markCurrentState;

    /**
     * Constructor.
     *
     * @param boolean     $renderProperties flag
     * @param string|null $markCurrentState fillcolor
     */
    public function __construct($renderProperties = false, $markCurrentState = null)
    {
        $this->renderProperties = (bool) $renderProperties;
        $this->markCurrentState = $markCurrentState;
    }

    /**
     * Returns whether state properties shall be rendered or not.
     *
     * @return boolean
     */
    public function renderProperties()
    {
        return $this->renderProperties;
    }

    /**
     * Returns in which color the current state shall be rendered (fillcolor) or null.
     *
     * @return string|null
     */
    public function markCurrentState()
    {
        return $this->markCurrentState;
    }

}
