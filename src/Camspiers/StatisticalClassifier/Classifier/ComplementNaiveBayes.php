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

use Camspiers\StatisticalClassifier\DataSource\DataSourceInterface;
use Camspiers\StatisticalClassifier\Model\Model;
use Camspiers\StatisticalClassifier\Model\ModelInterface;
use Camspiers\StatisticalClassifier\Normalizer\Lowercase;
use Camspiers\StatisticalClassifier\Normalizer\NormalizerInterface;
use Camspiers\StatisticalClassifier\Tokenizer\TokenizerInterface;
use Camspiers\StatisticalClassifier\Tokenizer\Word;
use Camspiers\StatisticalClassifier\Transform;

/**
 * An implementation of a Naive Bayes classifier.
 *
 * This classifier is based off *Tackling the Poor Assumptions of Naive Bayes Text Classifiers* by Jason Rennie
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
class ComplementNaiveBayes extends Classifier
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
     * Create the Naive Bayes Classifier
     * @param DataSourceInterface $dataSource
     * @param ModelInterface      $model      An model to store data in
     * @param TokenizerInterface  $tokenizer  The tokenizer to break up the documents
     * @param NormalizerInterface $normalizer The normaizer to make tokens consistent
     */
    public function __construct(
        DataSourceInterface $dataSource,
        ModelInterface $model = null,
        TokenizerInterface $tokenizer = null,
        NormalizerInterface $normalizer = null
    ) {
        $this->dataSource = $dataSource;
        $this->model = $model ?: new Model();
        $this->tokenizer = $tokenizer ?: new Word();
        $this->normalizer = $normalizer ?: new Lowercase();
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
        
        $tokensByCateory = $this->applyTransform(
            new Transform\TokensByCategory(
                $tokenCountByDocument
            )
        );
        
        $documentTokenCounts = $this->applyTransform(
            new Transform\DocumentTokenCounts(
                $documentLength
            )
        );
        
        $complement = $this->applyTransform(
            new Transform\Complement(
                $documentLength,
                $tokensByCateory,
                $documentCount,
                $documentTokenCounts
            )
        );
        
        $this->model->setModel(
            $this->applyTransform(
                new Transform\Weight($complement)
            )
        );
    }
    /**
     * @param string $document
     * @return mixed|string
     */
    public function classify($document)
    {
        $results = array();
        
        $tokens = array_count_values(
            $this->normalizer->normalize(
                $this->tokenizer->tokenize(
                    $document
                )
            )
        );
        
        $weights = $this->preparedModel()->getModel();
        
        foreach (array_keys($weights) as $category) {
            $results[$category] = 0;
            foreach ($tokens as $token => $count) {
                if (array_key_exists($token, $weights[$category])) {
                    $results[$category] += $count * $weights[$category][$token];
                }
            }
        }
        
        asort($results, SORT_NUMERIC);

        return key($results);
    }
}
