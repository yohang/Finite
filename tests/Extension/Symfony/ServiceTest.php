<?php
declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony;

use Finite\Extension\Twig\FiniteExtension;
use Finite\StateMachine;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;

class ServiceTest extends KernelTestCase
{
    public function test_services_are_registered(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();

        $this->assertInstanceOf(StateMachine::class, $container->get(StateMachine::class));
    }

    protected static function getKernelClass(): string
    {
        return \AppKernel::class;
    }
}
