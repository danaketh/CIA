<?php

namespace danaketh\CIBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package danaketh\CIBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('danaketh_ci');

        $rootNode->children()
            ->arrayNode('services')
            ->prototype('scalar')
            ->end();

        $rootNode->children()
            ->arrayNode('plugins')
                ->prototype('array')
                    ->children()
                        ->scalarNode('class')->end()
                        ->scalarNode('name')->end()
                    ->end()
                ->end()
            ->end();

        $rootNode->children()
            ->arrayNode('env')
            ->prototype('scalar')
            ->end();

        return $treeBuilder;
    }
}
