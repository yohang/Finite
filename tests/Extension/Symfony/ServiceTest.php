<?php
declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony;

use Finite\Event\CanTransitionEvent;
use Finite\StateMachine;
use Finite\Tests\Extension\Symfony\Fixtures\Model\Document;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ServiceTest extends KernelTestCase
{
    public function test_services_are_registered(): void
    {
        $container = static::getContainer();

        $this->assertInstanceOf(StateMachine::class, $container->get(StateMachine::class));
        $this->assertInstanceOf(EventDispatcherInterface::class, $container->get(StateMachine::class)->getDispatcher());
    }

    public function test_it_uses_the_symfony_dispatcher(): void
    {
        $container = static::getContainer();

        /** @var StateMachine $stateMachine */
        $stateMachine = $container->get(StateMachine::class);
        $stateMachine->can(new Document, 'publish');

        /** @var TraceableEventDispatcher $debugDispatcher */
        $debugDispatcher = $container->get('debug.event_dispatcher');

        $this->assertSame(CanTransitionEvent::class, $debugDispatcher->getOrphanedEvents()[0]);
    }

    protected static function getKernelClass(): string
    {
        return \AppKernel::class;
    }
}
