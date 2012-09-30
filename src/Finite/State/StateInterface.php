<?php

namespace Finite\State;

/**
 * The base State Interface
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
interface StateInterface
{
    const
        TYPE_INITIAL = 'initial',
        TYPE_NORMAL  = 'normal',
        TYPE_FINAL   = 'final'
    ;

    /**
     * Returns the state name
     *
     * @return string
     */
    public function getName();

    /**
     * Returns if this state is the initial state
     *
     * @return boolean
     */
    public function isInitial();

    /**
     * Returns if this state is the final state
     *
     * @return mixed
     */
    public function isFinal();

    /**
     * Returns if this state is a normal state (!($this->isInitial() || $this->isFinal())
     *
     * @return mixed
     */
    public function isNormal();

    /**
     * Returns the state type
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the available transitions
     *
     * @return array
     */
    public function getTransitions();

    /**
     * Returns if this state can run $transition
     *
     * @param string|\Finite\Transition\TransitionInterface $transition
     *
     * @return boolean
     */
    public function can($transition);

    /**
     * Returns optional state properties
     *
     * @return mixed
     */
    public function getProperties();
}
