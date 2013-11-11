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
     * @param string $targetFile       full path to the rendered output
     * @param bool   $renderProperties flag
     * @param bool   $markCurrentState flag
     */
    public function __construct($targetFile, $renderProperties = false, $markCurrentState = false)
    {
        $this->targetFile = $targetFile;
        $this->renderProperties = (bool) $renderProperties;
        $this->markCurrentState = (bool) $markCurrentState;
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
     * @return bool
     */
    public function renderProperties()
    {
        return $this->renderProperties;
    }

    /**
     * Returns whether the current state shall be rendered or not.
     * 
     * @return bool
     */
    public function markCurrentState()
    {
        return $this->markCurrentState;
    }

}
