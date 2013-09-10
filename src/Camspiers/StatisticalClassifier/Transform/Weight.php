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
class Weight implements TransformInterface
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function apply(DataSourceInterface $dataSource)
    {
        foreach ($this->data as $category => $tokens) {
            foreach ($tokens as $token => $value) {
                $this->data[$category][$token] = log($value, 10);
            }
        }
        return $this->data;
    }
}
