<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\Console\Command\Train;

use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class DocumentCommand extends Command
{
    /**
     * Configure the commands options
     * @return null
     */
    protected function configure()
    {
        $this
            ->setName('train:document')
            ->setDescription('Train the classifier with a document')
            ->configureModel()
            ->addArgument(
                'category',
                Input\InputArgument::REQUIRED,
                'Which category this data is'
            )
            ->addArgument(
                'document',
                Input\InputArgument::REQUIRED,
                'The document to train on'
            )
            ->configureClassifier()
            ->configurePrepare();
    }
    /**
     * Train a classifier with a single document
     * @param  Input\InputInterface   $input  The commands input
     * @param  Output\OutputInterface $output The commands output
     * @return null
     */
    protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
    {
        $modelName = $input->getArgument('model');
        
        $dataSource = $this->getDataSource($modelName);
        
        $dataSource->addDocument(
            $input->getArgument('category'),
            $input->getArgument('document')
        );

        $this->cacheDataSource($modelName);
        
        if ($input->getOption('prepare')) {
            $this->getClassifier($input)->prepareModel();
        }
        
        $output->writeLn("Document added to model");
    }
}
