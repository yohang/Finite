<?php

namespace Finite\Bundle\FiniteBundle\DependencyInjection\Compiler;

use Finite\Event\Callback\Callback;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Replace '@xxx' with the defined service if exists in loader configs.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ContainerCallbackPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $loaders = $container->findTaggedServiceIds('finite.loader');

        foreach ($loaders as $id => $loader) {
            $definition = $container->getDefinition($id);
            $config = $definition->getArgument(0);
            if (isset($config['callbacks'])) {
                foreach (array(Callback::CLAUSE_BEFORE, Callback::CLAUSE_AFTER) as $position) {
                    foreach ($config['callbacks'][$position] as &$callback) {
                        if (
                            is_array($callback[Callback::CLAUSE_DO])
                            && 0 === strpos($callback[Callback::CLAUSE_DO][0], '@')
                            && $container->hasDefinition(substr($callback[Callback::CLAUSE_DO][0], 1))
                        ) {
                            $callback[Callback::CLAUSE_DO][0] = new Reference(substr($callback[Callback::CLAUSE_DO][0], 1));
                        }
                    }
                }

                $definition->replaceArgument(0, $config);
            }
        }
    }
}
