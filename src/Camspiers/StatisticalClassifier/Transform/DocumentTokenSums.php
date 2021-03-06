<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\Transform;

use Camspiers\StatisticalClassifier\Index\IndexInterface;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class DocumentTokenSums implements TransformInterface
{
    const PARTITION_NAME = 'document_token_sums';

    private $dataPartitionName;

    public function __construct($dataPartitionName)
    {
        $this->dataPartitionName = $dataPartitionName;
    }

    public function apply(IndexInterface $index)
    {
        $data = $index->getPartition($this->dataPartitionName);
        $transform = array();

        foreach ($data as $category => $documents) {
            $transform[$category] = array();
            foreach ($documents as $docIndex => $document) {
                $transform[$category][$docIndex] = array_sum($document);
            }
        }

        $index->setPartition(self::PARTITION_NAME, $transform);
    }
}
