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
                ->arrayNode('drive_connection')
                    ->children()
                        ->scalarNode('path_to_refresh_token')->end()
                        ->scalarNode('auth_config')->end()
                    ->end()
                ->end()
                ->arrayNode('drive_folder_as_status')
                    ->children()
                        ->scalarNode('root_folder')->end()
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
