<?php

namespace Finite;

use Finite\StateMachine\StateMachineInterface;

/**
 * Interface that represents the contract between context
 *
 * @author Luis Henrique Mulinari <luis.mulinari@gmail.com>
 */
interface ContextInterface
{
    /**
     * @param object $object
     * @param string $graph
     *
     * @return string
     */
    public function getState($object, $graph = 'default');

    /**
     * @param object $object
     * @param string $graph
     * @param bool   $asObject
     *
     * @return array<string>
     */
    public function getTransitions($object, $graph = 'default', $asObject = false);

    /**
     * @param object $object
     * @param string $graph
     *
     * @return array<string>
     */
    public function getProperties($object, $graph = 'default');

    /**
     * @param object $object
     * @param string $property
     * @param string $graph
     *
     * @return bool
     */
    public function hasProperty($object, $property, $graph = 'default');

    /**
     * @param object $object
     * @param string $graph
     *
     * @return StateMachineInterface
     */
    public function getStateMachine($object, $graph = 'default');
}

