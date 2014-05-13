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
        $this->stateMachine = $this->getMock('Finite\StateMachine\StateMachine');
    }

    public function testItSupportsFrom()
    {
        $spec = new CallbackSpecification(array('s1', 's2'), array(), array(), function() {});

        $this->assertTrue($spec->supports($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->supports($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->supports($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification(array('-s3'), array(), array(), function() {});

        $this->assertTrue($spec->supports($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->supports($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->supports($this->getTransitionEvent('s3', 't34', 's4')));
    }

    public function testItSupportsTo()
    {
        $spec = new CallbackSpecification(array(), array('s2', 's3'), array(), function() {});

        $this->assertTrue($spec->supports($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->supports($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->supports($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification(array(), array('-s4'), array(), function() {});

        $this->assertTrue($spec->supports($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->supports($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->supports($this->getTransitionEvent('s3', 't34', 's4')));
    }

    public function testItSupportsOn()
    {
        $spec = new CallbackSpecification(array(), array(), array('t12', 't23'), function() {});

        $this->assertTrue($spec->supports($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->supports($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->supports($this->getTransitionEvent('s3', 't34', 's4')));

        $spec = new CallbackSpecification(array(), array(), array('-t34'), function() {});

        $this->assertTrue($spec->supports($this->getTransitionEvent('s1', 't12', 's2')));
        $this->assertTrue($spec->supports($this->getTransitionEvent('s2', 't23', 's3')));
        $this->assertFalse($spec->supports($this->getTransitionEvent('s3', 't34', 's4')));
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
