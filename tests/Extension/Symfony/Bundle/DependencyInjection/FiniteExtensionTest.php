<?php

declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony\Bundle\DependencyInjection;

use Finite\Extension\Symfony\Bundle\DependencyInjection\FiniteExtension;
use Finite\Extension\Twig\FiniteExtension as TwigExtension;
use Finite\Extractor\StatePropertyExtractor;
use Finite\StateMachine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FiniteExtensionTest extends TestCase
{
    public function testItLoadsServices(): void
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects($this->once())->method('addDefinitions')->with(
            $this->logicalAnd(
                $this->countOf(3),
                $this->arrayHasKey(StatePropertyExtractor::class),
                $this->arrayHasKey(StateMachine::class),
                $this->arrayHasKey(TwigExtension::class),
                $this->callback(function (array $definitions): bool {
                    $this->assertTrue($definitions[StateMachine::class]->isPublic());

                    return true;
                }),
            ),
        );

        $extension = new FiniteExtension();
        $extension->load([], $container);
    }
}
