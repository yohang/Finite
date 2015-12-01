<?php

namespace Finite\Event\Callback;

use Finite\StateMachine\StateMachineInterface;

/**
 * Builds a Callback instance.
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackBuilder
{
    /**
     * @var StateMachineInterface
     */
    private $stateMachine;

    /**
     * @var array
     */
    private $from;

    /**
     * @var array
     */
    private $to;

    /**
     * @var array
     */
    private $on;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @param StateMachineInterface $sm
     * @param array                 $from
     * @param array                 $to
     * @param array                 $on
     * @param callable              $callable
     */
    public function __construct(StateMachineInterface $sm, array $from = array(), array $to = array(), array $on = array(), $callable = null)
    {
        $this->stateMachine = $sm;
        $this->from = $from;
        $this->to = $to;
        $this->on = $on;
        $this->callable = $callable;
    }

    /**
     * @param array $from
     *
     * @return CallbackBuilder
     */
    public function setFrom(array $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param array $to
     *
     * @return CallbackBuilder
     */
    public function setTo(array $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param array $on
     *
     * @return CallbackBuilder
     */
    public function setOn(array $on)
    {
        $this->on = $on;

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return CallbackBuilder
     */
    public function setCallable($callable)
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * @param string $from
     *
     * @return CallbackBuilder
     */
    public function addFrom($from)
    {
        $this->from[] = $from;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return CallbackBuilder
     */
    public function addTo($to)
    {
        $this->to[] = $to;

        return $this;
    }

    /**
     * @param string $on
     *
     * @return CallbackBuilder
     */
    public function addOn($on)
    {
        $this->from[] = $on;

        return $this;
    }

    /**
     * @return Callback
     */
    public function getCallback()
    {
        return new Callback(
            new CallbackSpecification($this->stateMachine, $this->from, $this->to, $this->on),
            $this->callable
        );
    }

    /**
     * @param StateMachineInterface $sm
     * @param array                 $from
     * @param array                 $to
     * @param array                 $on
     * @param callable              $callable
     *
     * @return CallbackBuilder
     */
    public static function create(StateMachineInterface $sm, array $from = array(), array $to = array(), array $on = array(), $callable = null)
    {
        return new self($sm, $from, $to, $on, $callable);
    }
}
