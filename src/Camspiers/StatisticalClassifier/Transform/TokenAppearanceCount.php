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
class TokenAppearanceCount implements TransformInterface
{
    protected $tokenCountByDocument;
    
    public function __construct($tokenCountByDocument)
    {
        $this->tokenCountByDocument = $tokenCountByDocument;
    }

    public function apply(DataSourceInterface $dataSource)
    {
        $transform = array();
        foreach ($this->tokenCountByDocument as $documents) {
            foreach ($documents as $document) {
                foreach ($document as $token => $count) {
                    if ($count > 0) {
                        if (!array_key_exists($token, $transform)) {
                            $transform[$token] = 0;
                        }
                        $transform[$token]++;
                    }
                }
            }
        }
        return $transform;
    }
}
