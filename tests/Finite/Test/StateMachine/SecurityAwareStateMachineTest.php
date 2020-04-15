<?php

namespace Finite\Test\StateMachine;

use Finite\State\Accessor\StateAccessorInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\SecurityAwareStateMachine;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class SecurityAwareStateMachineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SecurityAwareStateMachine
     */
    protected $object;

    protected $accessor;

    /**
     * @throws \Finite\Exception\ObjectException
     */
    public function setUp()
    {
        $this->accessor = $this->createMock(StateAccessorInterface::class);
        $statefulMock = $this->createMock(StatefulInterface::class);
        $this->accessor->expects($this->at(0))
            ->method('getState')
            ->willReturn('s1')
        ;

        $this->object = new SecurityAwareStateMachine($statefulMock, null, $this->accessor);
        $this->object->addTransition('t12', 's1', 's2');
        $this->object->addTransition('t23', 's2', 's3');
        $this->object->initialize();
    }

    public function testCan()
    {
        $securityMock = $this->createMock(AuthorizationCheckerInterface::class);
        $this->object->setSecurityContext($securityMock);

        $that = $this;
        $stateful = $this->object->getObject();
        $addIsGrandedExpectation = static function ($return, $transition) use ($that, $securityMock, $stateful) {
            static $at = 0;

            $securityMock
                ->expects($that->at($at++))
                ->method('isGranted')
                ->with(...[$transition, $stateful])
                ->willReturn($return)
            ;
        };

        $addIsGrandedExpectation(true, 't12');
        $addIsGrandedExpectation(true, 't23');
        $addIsGrandedExpectation(false, 't12');
        $addIsGrandedExpectation(true, 't23');

        $this->assertTrue($this->object->can('t12'));
        $this->assertFalse($this->object->can('t23'));
        $this->assertFalse($this->object->can('t12'));
        $this->assertFalse($this->object->can('t23'));
    }
}
