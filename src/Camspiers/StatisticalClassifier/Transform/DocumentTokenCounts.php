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
class DocumentTokenCounts implements TransformInterface
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function apply(DataSourceInterface $dataSource)
    {
        $transform = array();

        foreach ($this->data as $category => $documents) {
            $transform[$category] = 0;
            foreach ($documents as $document) {
                $transform[$category] += count($document);
            }
        }
        
        return $transform;
    }
}
