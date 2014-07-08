<?php

namespace danaketh\CIBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class danakethCIExtension
 *
 * @package danaketh\CIBundle\DependencyInjection
 */
class danakethCIExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('danaketh_ci.services', $config['services']);
        $container->setParameter('danaketh_ci.plugins', $config['plugins']);
        $container->setParameter('danaketh_ci.env', $config['env']);
    }
}
