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
use Camspiers\StatisticalClassifier\Normalizer\NormalizerInterface;
use Camspiers\StatisticalClassifier\Tokenizer\TokenizerInterface;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class TokenCountByDocument implements TransformInterface
{
    protected $tokenizer;
    protected $normalizer;
    
    public function __construct(
        TokenizerInterface $tokenizer,
        NormalizerInterface $normalizer
    ) {
        $this->tokenizer = $tokenizer;
        $this->normalizer = $normalizer;
    }

    public function apply(DataSourceInterface $dataSource)
    {
        $transform = array();
        foreach ($dataSource->getData() as $document) {
            if (!isset($transform[$document['category']])) {
                $transform[$document['category']] = array();
            }
            $transform[$document['category']][] = array_count_values(
                $this->normalizer->normalize(
                    $this->tokenizer->tokenize(
                        $document['document']
                    )
                )
            );
        }
        return $transform;
    }
}
