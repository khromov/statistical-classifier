<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\Console\Command\Test;

use Camspiers\StatisticalClassifier\Classifier\ClassifierInterface;
use Camspiers\StatisticalClassifier\Console\Command\Command as BaseCommand;
use Camspiers\StatisticalClassifier\DataSource\DataSourceInterface;
use Symfony\Component\Console\Output;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
abstract class Command extends BaseCommand
{
    /**
     * Classifies a data source against a classifier and outputs results
     * @param  Output\OutputInterface $output     Output object
     * @param  ClassifierInterface    $classifier The classifier to use
     * @param  DataSourceInterface    $data       The data source
     * @return null
     */
    protected function test(
        Output\OutputInterface $output,
        ClassifierInterface $classifier,
        DataSourceInterface $data
    ) {
        $correct = array();
        $totalDocs = 0;

        foreach ($data->getData() as $category => $documents) {
            $correct[$category] = 0;
            $docCount = count($documents);
            $totalDocs += $docCount;
            foreach ($documents as $document) {
                if ($classifier->classify($document) == $category) {
                    $correct[$category]++;
                }
            }
            $output->writeLn($category . ': ' . ($correct[$category] / $docCount));
        }
        $output->writeLn('Total: ' . (array_sum($correct) / $totalDocs));
    }
}
