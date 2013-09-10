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

use Camspiers\StatisticalClassifier\DataSource\DataSourceInterface;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class TFIDF implements TransformInterface
{
    private $tokenCountByDocument;
    private $documentCount;
    private $tokenAppreanceCount;

    public function __construct(
        $tokenCountByDocument,
        $documentCount,
        $tokenAppreanceCount
    ) {
        $this->tokenCountByDocument = $tokenCountByDocument;
        $this->documentCount = $documentCount;
        $this->tokenAppreanceCount = $tokenAppreanceCount;
    }

    public function apply(DataSourceInterface $dataSource)
    {
        $transform = $this->tokenCountByDocument;
        foreach ($this->tokenCountByDocument as $category => $documents) {
            foreach ($documents as $documentModel => $document) {
                foreach ($document as $token => $count) {
                    $transform
                    [$category]
                    [$documentModel]
                    [$token] = log($count + 1, 10) * log(
                        $this->documentCount / $this->tokenAppreanceCount[$token],
                        10
                    );
                }
            }
        }
        
        return $transform;
    }
}
