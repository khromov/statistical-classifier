<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\Console\Command;

use CacheCache\Cache;
use Camspiers\StatisticalClassifier\Classifier\ClassifierInterface;
use Camspiers\StatisticalClassifier\Config\Config;
use Camspiers\StatisticalClassifier\Index\CachedIndex;
use Camspiers\StatisticalClassifier\Index\IndexInterface;
use Camspiers\StatisticalClassifier\Index\SVMCachedIndex;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
abstract class Command extends BaseCommand
{
    /**
     * Holds the config from the config.json files
     * @var array
     */
    protected $config;
    /**
     * Holds the CacheCache\Cache instance
     * @var Cache
     */
    protected $cache;
    /**
     * Holds the container instance for caching
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * Holds the classifier instance for caching
     * @var ClassifierInterface
     */
    protected $classifier;
    /**
     * Adds options nessacary for calling getClassifier in a command
     * @return Command This command to allow chaining
     */
    protected function configureClassifier()
    {
        $this
            ->addOption(
                'classifier',
                'c',
                Input\InputOption::VALUE_OPTIONAL,
                'Name of classifier',
                'complement_naive_bayes'
            );

        return $this;
    }
    /**
     * Adds arguments required for using a specified index in a command
     * @return Command This command to allow chaining
     */
    protected function configureIndex()
    {
        $this
            ->addArgument(
                'index',
                Input\InputArgument::REQUIRED,
                'Name of index'
            );

        return $this;
    }
    /**
     * Adds options to allow automatically prepare the index
     * @return Command This command to allow chaining
     */
    protected function configurePrepare()
    {
        $this
            ->addOption(
                'prepare',
                'p',
                Input\InputOption::VALUE_NONE,
                'Prepare the index after training'
            );

        return $this;
    }
    /**
     * Allow for cache to be stored on command for setter injection
     * @param Cache $cache The cache to store
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }
    /**
     * Get an CachedIndex based off a index name and the Cache instance
     * @param  string      $name The name of the index
     * @return CachedIndex The cached index
     */
    protected function getCachedIndex($name)
    {
        return new CachedIndex(
            $name,
            $this->cache
        );
    }
    /**
     * Get a SVMCachedIndex based of an index name and the Cache instance
     * @param $name
     * @return SVMCachedIndex
     */
    protected function getSVMCachedIndex($name)
    {
        return new SVMCachedIndex(
            Config::getClassifierPath() . "/indexes/$name.svm",
            $name,
            $this->cache
        );
    }
    /**
     * Return the dependency injection container fetching it off the app if it doesn't exist
     * @return Symfony\Component\DependencyInjection\ContainerInterface The container
     */
    protected function getContainer()
    {
        if (null === $this->container) {
            $this->container = $this->getApplication()->getContainer();
        }

        return $this->container;
    }
    /**
     * Returns a classifier based of the commands input and the specified index (if exists)
     * @param  Input\InputInterface $input The commands input
     * @param  IndexInterface       $index Optional index to use in the classifier
     * @return ClassifierInterface  The built classifier
     */
    protected function getClassifier(Input\InputInterface $input)
    {
        if (null === $this->classifier) {
            $container = $this->getContainer();
            $classifier = 'classifier.' . $input->getOption('classifier');
            if ($container->has($classifier)) {
                if ($classifier == 'classifier.svm') {
                    $index = $this->getSVMCachedIndex(
                        $input->getArgument('index')
                    );
                } else {
                    $index = $this->getCachedIndex(
                        $input->getArgument('index')
                    );
                }
                $container->set(
                    'index.index',
                    $index
                );
                $this->classifier = $container->get($classifier);
            } else {
                throw new \RuntimeException("Classifier '$classifier' doesn't exist");
            }
        }

        return $this->classifier;
    }
}
