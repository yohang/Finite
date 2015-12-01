<?php

namespace Finite\Bundle\FiniteBundle\DependencyInjection\Compiler;

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
                foreach (array('before', 'after') as $position) {
                    foreach ($config['callbacks'][$position] as &$callback) {
                        if (
                            is_array($callback['do'])
                            && 0 === strpos($callback['do'][0], '@')
                            && $container->hasDefinition(substr($callback['do'][0], 1))
                        ) {
                            $callback['do'][0] = new Reference(substr($callback['do'][0], 1));
                        }
                    }
                }

                $definition->replaceArgument(0, $config);
            }
        }
    }
}
