<?php

namespace Finite\Bundle\FiniteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('finite_finite');
        $rootNode = $treeBuilder->getRootNode();
        $rootProto = $rootNode->useAttributeAsKey('name')->prototype('array')->children();

        $rootProto
            ->scalarNode('class')->isRequired()->end()
            ->scalarNode('graph')->defaultValue('default')->end()
            ->scalarNode('property_path')->defaultValue('finiteState')->end()
        ;

        $this->addStateSection($rootProto);
        $this->addTransitionSection($rootProto);
        $this->addCallbackSection($rootProto);
        $rootProto->end()->end();

        return $treeBuilder;
    }

    /**
     * @param NodeBuilder $rootProto
     */
    protected function addStateSection(NodeBuilder $rootProto): void
    {
        $rootProto
            ->arrayNode('states')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
            ->scalarNode('type')->defaultValue('normal')->end()
            ->arrayNode('properties')
            ->useAttributeAsKey('name')
            ->defaultValue([])
            ->prototype('variable')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }

    /**
     * @param NodeBuilder $rootProto
     */
    protected function addTransitionSection(NodeBuilder $rootProto): void
    {
        $rootProto
            ->arrayNode('transitions')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
            ->arrayNode('from')
            ->prototype('variable')->end()
            ->end()
            ->scalarNode('to')->end()
            ->arrayNode('properties')
            ->useAttributeAsKey('name')
            ->defaultValue([])
            ->prototype('variable')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }

    /**
     * @param NodeBuilder $rootProto
     */
    protected function addCallbackSection(NodeBuilder $rootProto): void
    {
        $callbacks = $rootProto->arrayNode('callbacks')->children();
        $this->addSubCallbackSection($callbacks, 'before');
        $this->addSubCallbackSection($callbacks, 'after');
        $callbacks->end()->end();
    }

    /**
     * @param NodeBuilder $callbacks
     * @param string      $type
     */
    private function addSubCallbackSection(NodeBuilder $callbacks, $type): void
    {
        $callbacks
            ->arrayNode($type)
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
            ->scalarNode('on')->end()
            ->variableNode('do')->end()
            ->variableNode('from')->end()
            ->variableNode('to')->end()
            ->scalarNode('disabled')->defaultValue(false)->end()
            ->end()
            ->end()
            ->end()
        ;
    }
}
