<?php

namespace Jasdero\PassePlatBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jasdero_passe_plat');

        $rootNode
            ->children()
                ->booleanNode('activation')->isRequired()->end()
                ->arrayNode('drive_folder_as_status')->canBeUnset()->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('root_folder')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('folders')->canBeUnset()->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('to_scan')->defaultNull()->end()
                        ->scalarNode('new_orders')->defaultNull()->end()
                        ->scalarNode('errors')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('credentials')->canBeUnset()->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path_to_refresh_token')->defaultNull()->end()
                        ->scalarNode('auth_config')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
