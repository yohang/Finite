<?php

namespace Finite\Bundle\FiniteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FiniteFiniteExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config as $key => $stateMachineConfig)
        {
            $container->setDefinition(
                'finite.state_machine.'.$key,
                $this->buildStateMachineDefinition($key, $container, $stateMachineConfig)
            );
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @return Definition
     */
    protected function buildStateMachineDefinition($name, ContainerBuilder $container, array $config)
    {
        $definition = $container->getDefinition('finite.state_machine');
        $definition = clone $definition;

        $container->setDefinition('finite.array_loader.'.$name, $this->buildLoaderDefinition($container, $config));
        $definition->setPublic(true);
        $definition->addMethodCall('load', array(new Reference('finite.array_loader.'.$name)));

        return $definition;
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @return Definition
     */
    protected function buildLoaderDefinition(ContainerBuilder $container, array $config)
    {
        $definition = $container->getDefinition('finite.array_loader');
        $definition = clone $definition;

        $definition->addArgument($config);

        return $definition;
    }
}
