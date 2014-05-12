<?php

namespace Finite\Test\Acceptance;

use Finite\Loader\ArrayLoader;
use Finite\StateMachine\StateMachine;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbacksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StateMachine
     */
    protected $stateMachine;

    /**
     * @var StateMachine
     */
    protected $alternativeStateMachine;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $callbacksMock;

    protected $object;

    protected $alternativeObject;

    protected function setUp()
    {
        $this->object              = new \stdClass;
        $this->object->finiteState = null;

        $this->alternativeObject              = new \stdClass;
        $this->alternativeObject->finiteState = null;

        $this->stateMachine            = new StateMachine($this->object);
        $this->alternativeStateMachine = new StateMachine($this->alternativeObject, $this->stateMachine->getDispatcher());

        $this->callbacksMock = $this
            ->getMockBuilder('\stdClass')
            ->setMethods(
                array(
                    'afterItWasProposed',
                    'afterItWasProposedOrReviewed',
                    'afterItWasAnythingButProposed',
                    'onReview',
                    'afterItLeavesReviewed'
                )
            )
            ->getMock();

        $states = array(
            'draft'     => array('type' => 'initial'),
            'proposed'  => array(),
            'reviewed'  => array(),
            'published' => array('type' => 'final'),
            'declined'  => array('type' => 'final'),
        );

        $transitions = array(
            'propose'         => array('from' => 'draft', 'to' => 'proposed'),
            'return_to_draft' => array('from' => 'proposed', 'to' => 'draft'),
            'review'          => array('from' => 'proposed', 'to' => 'reviewed'),
            'publish'         => array('from' => 'reviewed', 'to' => 'published'),
            'decline'         => array('from' => array('proposed', 'reviewed'), 'to' => 'declined'),
        );

        $loader = new ArrayLoader(
            array(
                'states'      => $states,
                'transitions' => $transitions,
                'callbacks'   => array(
                    'after' => array(
                        array(
                            'to' => 'proposed',
                            'do' => array($this->callbacksMock, 'afterItWasProposed'),
                        ),
                        array(
                            'to' => array('proposed', 'reviewed'),
                            'do' => array($this->callbacksMock, 'afterItWasProposedOrReviewed'),
                        ),
                        array(
                            'to' => array('-proposed'),
                            'do' => array($this->callbacksMock, 'afterItWasAnythingButProposed'),
                        ),
                        array(
                            'on' => 'review',
                            'do' => array($this->callbacksMock, 'onReview'),
                        ),
                        array(
                            'from' => 'reviewed',
                            'do'   => array($this->callbacksMock, 'afterItLeavesReviewed'),
                        ),
                    )
                )
            )
        );

        $loader->load($this->stateMachine);
        $this->stateMachine->initialize();

        $alternativeLoader = new ArrayLoader(array('states' => $states, 'transitions' => $transitions));

        $alternativeLoader->load($this->alternativeStateMachine);
        $this->alternativeStateMachine->initialize();
    }

    public function test()
    {
        $this->callbacksMock->expects($this->once())->method('afterItWasProposed');
        $this->callbacksMock->expects($this->exactly(2))->method('afterItWasProposedOrReviewed');
        $this->callbacksMock->expects($this->exactly(2))->method('afterItWasAnythingButProposed');
        $this->callbacksMock->expects($this->once())->method('onReview');
        $this->callbacksMock->expects($this->once())->method('afterItLeavesReviewed');

        $this->stateMachine->apply('propose');
        $this->stateMachine->apply('review');
        $this->stateMachine->apply('publish');

        $this->alternativeStateMachine->apply('propose');
        $this->alternativeStateMachine->apply('review');
        $this->alternativeStateMachine->apply('publish');
    }
}
