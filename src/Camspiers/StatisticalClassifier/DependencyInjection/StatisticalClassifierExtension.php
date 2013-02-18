<?php

namespace Camspiers\StatisticalClassifier\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

class StatisticalClassifierExtension extends BaseExtension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../../../config'));
        $loader->load($this->getAlias() . '_services.yml');
    }
}