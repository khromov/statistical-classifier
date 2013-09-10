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
class Complement implements TransformInterface
{
    protected $documentLength;
    protected $tokensByCategory;
    protected $documentCount;
    protected $documentTokenCounts;
    
    public function __construct(
        $documentLength,
        $tokensByCategory,
        $documentCount,
        $documentTokenCounts
    ) {
        $this->documentLength = $documentLength;
        $this->tokensByCategory = $tokensByCategory;
        $this->documentCount = $documentCount;
        $this->documentTokenCounts = $documentTokenCounts;
    }

    public function apply(DataSourceInterface $dataSource)
    {
        $cats = array_keys($this->tokensByCategory);
        $trans = array();

        $tokByCatSums = array();

        foreach ($this->tokensByCategory as $cat => $tokens) {
            $tokByCatSums[$cat] = array_sum($tokens);
        }

        $documentCounts = array();

        foreach ($this->documentLength as $cat => $documents) {
            $documentCounts[$cat] = count($documents);
        }

        foreach ($this->tokensByCategory as $cat => $tokens) {

            $trans[$cat] = array();
            $categoriesSelection = array_diff($cats, array($cat));

            $docsInOtherCats = $this->documentCount - $documentCounts[$cat];

            foreach (array_keys($tokens) as $token) {
                $trans[$cat][$token] = $docsInOtherCats;
                foreach ($categoriesSelection as $currCat) {
                    if (array_key_exists($token, $this->tokensByCategory[$currCat])) {
                        $trans[$cat][$token] += $this->tokensByCategory[$currCat][$token];
                    }
                }
                foreach ($categoriesSelection as $currCat) {
                    $trans[$cat][$token] = $trans[$cat][$token] / ($tokByCatSums[$currCat] + $this->documentTokenCounts[$currCat]);
                }

            }

        }
        
        return $trans;
    }
}
