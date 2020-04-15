<?php

namespace Finite\Test\Event\Callback;

use Finite\Event\Callback\CallbackSpecification;
use Finite\Event\TransitionEvent;
use Finite\State\State;
use Finite\StateMachine\StateMachine;
use Finite\Transition\Transition;
use PHPUnit_Framework_TestCase;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbackSpecificationTest extends PHPUnit_Framework_TestCase
{
    private $stateMachine;

    protected function setUp()
    {
        $this->stateMachine = $this->createMock(StateMachine::class);
    }

    public function testItIsSatisfiedByFrom()
    {
        $spec = new CallbackSpecification($this->stateMachine, ['s1', 's2'], [], []);

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification($this->stateMachine, ['-s3'], [], []);

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));
    }

    public function testItIsSatisfiedByTo()
    {
        $spec = new CallbackSpecification($this->stateMachine, [], ['s2', 's3'], []);

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification($this->stateMachine, [], ['-s4'], []);

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));
    }

    public function testItIsSatisfiedByOn()
    {
        $spec = new CallbackSpecification($this->stateMachine, [], [], ['t12', 't23']);

        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->isSatisfiedBy($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->isSatisfiedBy($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification($this->stateMachine, [], [], ['-t34']);

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
            new Transition($transition, [$fromState], $toState),
            $this->stateMachine
        );
    }
}
