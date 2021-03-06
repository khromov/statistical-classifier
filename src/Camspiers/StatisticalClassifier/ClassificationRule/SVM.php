<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\ClassificationRule;

use Camspiers\StatisticalClassifier\Transform\SVM as TransformSVM;
use Camspiers\StatisticalClassifier\Index\IndexInterface;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class SVM implements ClassificationRuleInterface
{
    /**
     * Classifies a document against an index
     * @param  IndexInterface $index    The Index to classify against
     * @param  string         $document The document to classify
     * @return string         The category of the document
     */
    public function classify(IndexInterface $index, $document)
    {
        $categoryMap = $index->getPartition(TransformSVM::CATEGORY_MAP_PARITITION_NAME);
        $tokenMap = $index->getPartition(TransformSVM::TOKEN_MAP_PARITITION_NAME);
        $model = $index->getPartition(TransformSVM::MODEL_PARTITION_NAME);

        $data = array();
        $tokenCounts = array_count_values($document);
        foreach ($tokenCounts as $token => $value) {
            if (isset($tokenMap[$token])) {
                $data[$tokenMap[$token]] = $value;
            }
        }
        ksort($data, SORT_NUMERIC);

        return $categoryMap[$model->predict($data)];
    }
}
