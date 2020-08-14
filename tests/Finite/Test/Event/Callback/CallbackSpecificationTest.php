<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\CallbackSpecification;
use Finite\Event\TransitionEvent;
use Finite\State\State;
use Finite\Transition\Transition;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackSpecificationTest extends \PHPUnit_Framework_TestCase
{
    private $stateMachine;

    protected function setUp()
    {
        $this->stateMachine = $this->createMock('Finite\StateMachine\StateMachine');
    }

    public function testItIsSatisfiedByFrom()
    {
        $spec = new CallbackSpecification($this->stateMachine, array('s1', 's2'), array(), array());

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification($this->stateMachine, array('-s3'), array(), array());

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));
    }

    public function testItIsSatisfiedByTo()
    {
        $spec = new CallbackSpecification($this->stateMachine, array(), array('s2', 's3'), array());

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification($this->stateMachine, array(), array('-s4'), array());

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));
    }

    public function testItIsSatisfiedByOn()
    {
        $spec = new CallbackSpecification($this->stateMachine, array(), array(), array('t12', 't23'));

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification($this->stateMachine, array(), array(), array('-t34'));

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));
    }

    /**
     * @param string $fromState
     * @param string $transition
     * @param string $toState
     *
     * @return TransitionEvent
     */
    private function getTransitionEvent($fromState, $transition, $toState)
    {
        return new TransitionEvent(
            new State($fromState),
            new Transition($transition, array($fromState), $toState),
            $this->stateMachine
        );
    }
}
