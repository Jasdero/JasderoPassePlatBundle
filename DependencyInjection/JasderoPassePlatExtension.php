<?php

namespace Jasdero\PassePlatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class JasderoPassePlatExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $container->setParameter( 'root_folder', $processedConfig['drive_folder_as_status'][ 'root_folder' ] );
        $container->setParameter( 'folder_to_scan', $processedConfig['folders']['to_scan'] );
        $container->setParameter( 'new_orders_folder', $processedConfig['folders']['new_orders'] );
        $container->setParameter( 'errors_folder', $processedConfig['folders']['errors'] );
        $container->setParameter( 'drive_activation', $processedConfig['activation'] );
        $container->setParameter('path_to_refresh_token', $processedConfig['credentials']['path_to_refresh_token']);
        $container->setParameter('auth_config', $processedConfig['credentials']['auth_config']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
