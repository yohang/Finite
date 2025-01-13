<?php
declare(strict_types=1);

namespace Finite\Extension\Symfony\Bundle\DependencyInjection;

use Finite\Extension\Twig\FiniteExtension as TwigExtension;
use Finite\StateMachine;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class FiniteExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->addDefinitions(
            [
                StateMachine::class => (new Definition(StateMachine::class))
                    ->setArgument('$dispatcher', new Reference('event_dispatcher'))
                    ->setPublic(true),
                TwigExtension::class => (new Definition(TwigExtension::class))
                    ->setArgument('$stateMachine', new Reference(StateMachine::class))
                    ->addTag('twig.extension'),
            ]
        );
    }
}
