<?php

namespace Finite\Bundle\FiniteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FiniteFiniteExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $factoryDefinition = $container->getDefinition('finite.factory');

        $smDefinition = $container->getDefinition('finite.state_machine');
        if (method_exists($smDefinition, 'setShared')) {
            $smDefinition->setShared(false);
        } else {
            $smDefinition->setScope('prototype');
        }

        foreach ($config as $key => $stateMachineConfig) {
            $stateMachineConfig = $this->removeDisabledCallbacks($stateMachineConfig);

            $definition = clone $container->getDefinition('finite.array_loader');
            $definition->replaceArgument(0, $stateMachineConfig);
            $definition->addTag('finite.loader');

            // setLazy method wasn't available before 2.3, FiniteBundle requirement is ~2.1
            if (method_exists($definition, 'setLazy')) {
                $definition->setLazy(true);
            }

            $serviceId = 'finite.loader.'.$key;
            $container->setDefinition($serviceId, $definition);

            $factoryDefinition->addMethodCall('addLoader', array(new Reference($serviceId)));
        }

        $container->removeDefinition('finite.array_loader');
    }

    /**
     * Remove callback entries where index 'disabled' is set to true.
     *
     * @param array $config
     *
     * @return array
     */
    protected function removeDisabledCallbacks(array $config)
    {
        if (!isset($config['callbacks'])) {
            return $config;
        }

        foreach (array('before', 'after') as $position) {
            foreach ($config['callbacks'][$position] as $i => $callback) {
                if ($callback['disabled']) {
                    unset($config['callbacks'][$position][$i]);
                }
                unset($config['callbacks'][$position][$i]['disabled']);
            }
        }

        return $config;
    }
}
