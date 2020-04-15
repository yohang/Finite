<?php

namespace Finite\Test\Factory;

use Finite\Factory\PimpleFactory;
use Finite\Loader\LoaderInterface;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use PHPUnit\Framework\TestCase;
use Pimple;

class PimpleFactoryTest extends TestCase
{
    /**
     * @var PimpleFactory
     */
    protected $object;

    protected $accessor;

    public function setUp(): void
    {
        $this->accessor = $accessor = $this->createMock(StateAccessorInterface::class);
        $container = new Pimple(
            [
                'state_machine' => static function () use ($accessor) {
                    $sm = new StateMachine(null, null, $accessor);
                    $sm->addTransition('t12', 's1', 's2');
                    $sm->addTransition('t23', 's2', 's3');

                    return $sm;
                },
            ]
        );

        $this->object = new PimpleFactory($container, 'state_machine');
    }

    public function testGet(): void
    {
        $object = $this->createMock(StatefulInterface::class);
        $this->accessor->expects($this->at(0))->method('getState')->willReturn('s2');
        $sm = $this->object->get($object);

        $this->assertInstanceOf(StateMachine::class, $sm);
        $this->assertSame('s2', $sm->getCurrentState()->getName());

        $object2 = $this->createMock(StatefulInterface::class);
        $this->accessor->expects($this->at(0))->method('getState')->willReturn('s2');
        $sm2 = $this->object->get($object2);

        $this->assertNotSame($sm, $sm2);
    }

    public function testLoad(): void
    {
        $object = $this->createMock(StatefulInterface::class);
        $this->accessor
            ->method('getState')
            ->willReturn('s1')
        ;

        $loader1 = $this->createMock(LoaderInterface::class);
        $loader1->expects($this->at(0))
            ->method('supports')
            ->with(...[$object, 'foo'])
            ->willReturn(false)
        ;
        $loader1->expects($this->at(1))
            ->method('supports')
            ->with(...[$object, 'bar'])
            ->willReturn(true)
        ;
        $loader2 = $this->createMock(LoaderInterface::class);
        $loader2->expects($this->at(0))
            ->method('supports')
            ->with(...[$object, 'foo'])
            ->willReturn(true)
        ;
        $loader2->expects($this->at(1))
            ->method('load')
        ;

        $this->object->addLoader($loader1);
        $this->object->addLoader($loader2);

        $this->object->get($object, 'foo');
        $this->object->get($object, 'bar');
    }
}
