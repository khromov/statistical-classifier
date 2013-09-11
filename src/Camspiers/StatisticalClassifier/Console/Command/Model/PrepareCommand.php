<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\Console\Command\Model;

use Camspiers\StatisticalClassifier\Console\Command\Command;
use RuntimeException;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class PrepareCommand extends Command
{
    /**
     * Configure the commands options
     * @return null
     */
    protected function configure()
    {
        $this
            ->setName('model:prepare')
            ->setDescription('Prepare an model')
            ->configureModel()
            ->configureClassifier();
    }
    /**
     * Prepare a specified model
     * @param  Input\InputInterface   $input  The input object
     * @param  Output\OutputInterface $output The output object
     * @throws \RuntimeException
     * @return null
     */
    protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
    {
        $model = $input->getArgument('model');
        if ($this->cache->exists($model)) {
            $this->getClassifier($input)->prepareModel();
            $output->writeLn("Model '$model' was prepared");
        } else {
            throw new RuntimeException("Model '$model' doesn't exist");
        }
    }
}
