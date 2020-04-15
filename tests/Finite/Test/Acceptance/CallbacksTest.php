<?php

namespace Finite\Test\Acceptance;

use Finite\Loader\ArrayLoader;
use Finite\StateMachine\StateMachine;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class CallbacksTest extends PHPUnit_Framework_TestCase
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

    /**
     * @throws \Finite\Exception\ObjectException
     */
    protected function setUp()
    {
        $this->object = new stdClass;
        $this->object->finiteState = null;

        $this->alternativeObject = new stdClass;
        $this->alternativeObject->finiteState = null;

        $this->stateMachine = new StateMachine($this->object);
        $this->alternativeStateMachine = new StateMachine($this->alternativeObject, $this->stateMachine->getDispatcher());

        $this->callbacksMock = $this
            ->getMockBuilder(stdClass::class)
            ->setMethods(
                [
                    'afterItWasProposed',
                    'afterItWasProposedOrReviewed',
                    'afterItWasAnythingButProposed',
                    'onReview',
                    'afterItLeavesReviewed',
                ]
            )
            ->getMock()
        ;

        $states = [
            'draft' => ['type' => 'initial'],
            'proposed' => [],
            'reviewed' => [],
            'published' => ['type' => 'final'],
            'declined' => ['type' => 'final'],
        ];

        $transitions = [
            'propose' => ['from' => 'draft', 'to' => 'proposed'],
            'return_to_draft' => ['from' => 'proposed', 'to' => 'draft'],
            'review' => ['from' => 'proposed', 'to' => 'reviewed'],
            'publish' => ['from' => 'reviewed', 'to' => 'published'],
            'decline' => ['from' => ['proposed', 'reviewed'], 'to' => 'declined'],
        ];

        $loader = new ArrayLoader(
            [
                'states' => $states,
                'transitions' => $transitions,
                'callbacks' => [
                    'after' => [
                        [
                            'to' => 'proposed',
                            'do' => [$this->callbacksMock, 'afterItWasProposed'],
                        ],
                        [
                            'to' => ['proposed', 'reviewed'],
                            'do' => [$this->callbacksMock, 'afterItWasProposedOrReviewed'],
                        ],
                        [
                            'to' => ['-proposed'],
                            'do' => [$this->callbacksMock, 'afterItWasAnythingButProposed'],
                        ],
                        [
                            'on' => 'review',
                            'do' => [$this->callbacksMock, 'onReview'],
                        ],
                        [
                            'from' => 'reviewed',
                            'do' => [$this->callbacksMock, 'afterItLeavesReviewed'],
                        ],
                    ],
                ],
            ]
        );

        $loader->load($this->stateMachine);
        $this->stateMachine->initialize();

        $alternativeLoader = new ArrayLoader(['states' => $states, 'transitions' => $transitions]);

        $alternativeLoader->load($this->alternativeStateMachine);
        $this->alternativeStateMachine->initialize();
    }

    /**
     * @throws \Finite\Exception\StateException
     */
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
