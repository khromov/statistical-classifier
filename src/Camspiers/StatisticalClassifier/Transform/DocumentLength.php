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
class DocumentLength implements TransformInterface
{
    protected $tfidf;
    
    public function __construct($tfidf)
    {
        $this->tfidf = $tfidf;
    }

    public function apply(DataSourceInterface $dataSource)
    {
        $transform = $this->tfidf;
        
        foreach ($this->tfidf as $category => $documents) {
            foreach ($documents as $documentIndex => $document) {
                $denominator = 0;
                foreach ($document as $count) {
                    $denominator += $count * $count;
                }
                $denominator = sqrt($denominator);
                foreach ($document as $token => $count) {
                    $transform
                        [$category]
                        [$documentIndex]
                        [$token] = $count / $denominator;
                }
            }
        }
        
        return $transform;
    }
}
