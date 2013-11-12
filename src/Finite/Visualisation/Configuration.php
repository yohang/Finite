<?php

namespace Finite\Visualisation;

/**
 * Configuration value object.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Configuration
{
    private $targetFile;
    private $renderProperties;
    private $markCurrentState;

    /**
     * Constructor.
     *
     * @param string      $targetFile       full path to the rendered output
     * @param boolean     $renderProperties flag
     * @param string|null $markCurrentState fillcolor
     */
    public function __construct($targetFile, $renderProperties = false, $markCurrentState = null)
    {
        $this->targetFile = $targetFile;
        $this->renderProperties = (bool) $renderProperties;
        $this->markCurrentState = $markCurrentState;
    }

    /**
     * Returns the target file path.
     *
     * @return string
     */
    public function getTargetFile()
    {
        return $this->targetFile;
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
