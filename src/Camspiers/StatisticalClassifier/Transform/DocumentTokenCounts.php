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
class DocumentTokenCounts implements TransformInterface
{
    const PARTITION_NAME = 'document_token_counts';

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
            $transform[$category] = 0;
            foreach ($documents as $document) {
                $transform[$category] += count($document);
            }
        }

        $index->setPartition(self::PARTITION_NAME, $transform);
    }
}
