<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\Classifier;

use Camspiers\StatisticalClassifier\ClassificationRule;
use Camspiers\StatisticalClassifier\DataSource\DataSourceInterface;
use Camspiers\StatisticalClassifier\Model\SVMModel;
use Camspiers\StatisticalClassifier\Normalizer\Lowercase;
use Camspiers\StatisticalClassifier\Normalizer\NormalizerInterface;
use Camspiers\StatisticalClassifier\Tokenizer\TokenizerInterface;
use Camspiers\StatisticalClassifier\Tokenizer\Word;
use Camspiers\StatisticalClassifier\Transform;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class SVM extends Classifier
{
    /**
     * Tokenizer (the way of breaking up documents)
     * @var TokenizerInterface
     */
    protected $tokenizer;
    /**
     * Take tokenized data and make it consistent or stem it
     * @var NormalizerInterface
     */
    protected $normalizer;
    /**
     * @param DataSourceInterface $dataSource
     * @param SVMModel            $model
     * @param TokenizerInterface  $tokenizer
     * @param NormalizerInterface $normalizer
     * @param \SVM                $svm
     */
    public function __construct(
        DataSourceInterface $dataSource,
        SVMModel $model = null,
        TokenizerInterface $tokenizer = null,
        NormalizerInterface $normalizer = null,
        \SVM $svm = null
    ) {
        $this->dataSource = $dataSource;
        $this->model = $model ?: new SVMModel();
        $this->tokenizer = $tokenizer ?: new Word();
        $this->normalizer = $normalizer ?: new Lowercase();
        if (!$svm) {
            $svm = new \SVM();
            $svm->setOptions(
                array(
                    102 => 0
                )
            );
        }
        $this->svm = $svm;
    }
    /**
     * {@inheritdoc}
     */
    public function prepareModel()
    {
        $tokenCountByDocument = $this->applyTransform(
            new Transform\TokenCountByDocument(
                $this->tokenizer,
                $this->normalizer
            )
        );

        $documentCount = $this->applyTransform(
            new Transform\DocumentCount()
        );

        $tokenAppearanceCount = $this->applyTransform(
            new Transform\TokenAppearanceCount(
                $tokenCountByDocument
            )
        );

        $tfidf = $this->applyTransform(
            new Transform\TFIDF(
                $tokenCountByDocument,
                $documentCount,
                $tokenAppearanceCount
            )
        );

        $documentLength = $this->applyTransform(
            new Transform\DocumentLength($tfidf)
        );

        $categoryMap = array();
        $categoryCount = 0;
        $tokenMap = array();
        $tokenCount = 1;

        // Produce the token and category maps for the whole document set
        foreach ($documentLength as $category => $documents) {
            if (!array_key_exists($category, $categoryMap)) {
                $categoryMap[$category] = $categoryCount;
                $categoryCount++;
            }
            foreach ($documents as $document) {
                foreach (array_keys($document) as $token) {
                    if (!array_key_exists($token, $tokenMap)) {
                        $tokenMap[$token] = $tokenCount;
                        $tokenCount++;
                    }
                }
            }
        }
        
        $transform = array();

        // Prep the svm data set for use
        foreach ($documentLength as $category => $documents) {
            foreach ($documents as $document) {
                $entry = array(
                    $categoryMap[$category]
                );
                foreach ($document as $token => $value) {
                    $entry[$tokenMap[$token]] = $value;
                }
                ksort($entry, SORT_NUMERIC);
                $transform[] = $entry;
            }
        }

        // Weight the data set by the number of docs that appear in each class.
        $weights = array();

        foreach ($documentLength as $category => $documents) {
            $weights[$categoryMap[$category]] = count($documents);
        }

        $lowest = min($weights);

        $weights = array_map(
            function ($weight) use ($lowest) {
                return $lowest / $weight;
            },
            $weights
        );

        $this->model->setMaps(array_flip($categoryMap), $tokenMap);
        
        $this->model->setModel(
            $this->svm->train(
                $transform,
                $weights
            )
        );
    }
    /**
     * @param string $document
     * @return mixed|string
     */
    public function classify($document)
    {   
        /** @var SVMModel $model */
        $model = $this->preparedModel();
        
        $tokenMap = $model->getTokenMap();
        $categoryMap = $model->getCategoryMap();

        $data = array();
        
        $tokenCounts = array_count_values(
            $this->normalizer->normalize(
                $this->tokenizer->tokenize(
                    $document
                )
            )
        );
        
        foreach ($tokenCounts as $token => $value) {
            if (isset($tokenMap[$token])) {
                $data[$tokenMap[$token]] = $value;
            }
        }
        
        ksort($data, SORT_NUMERIC);

        return $categoryMap[$model->getModel()->predict($data)];
    }
}
