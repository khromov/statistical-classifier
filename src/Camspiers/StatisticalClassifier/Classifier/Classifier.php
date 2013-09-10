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
use Camspiers\StatisticalClassifier\Model\ModelInterface;
use Camspiers\StatisticalClassifier\Transform\TransformInterface;
use RuntimeException;

/**
 * A generic classifier which can be used to built a classifier given a number of injected components
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
abstract class Classifier implements ClassifierInterface
{
    /**
     * @var DataSourceInterface
     */
    protected $dataSource;
    /**
     * The model to apply the transforms to
     * @var ModelInterface
     */
    protected $model;
    /**
     * {@inheritdoc}
     */
    public function is($category, $document)
    {
        if ($this->dataSource->hasCategory($category)) {
            return $this->classify($document) === $category;
        } else {
            throw new RuntimeException(
                sprintf(
                    "The category '%s' doesn't exist",
                    $category
                )
            );
        }
    }
    /**
     * Return an model which has been prepared for classification
     * @return ModelInterface
     */
    protected function preparedModel()
    {
        if (!$this->model->isPrepared()) {
            $this->prepareModel();
        }

        return $this->model;
    }
    /**
     * @param TransformInterface $transform
     * @return mixed
     */
    protected function applyTransform(TransformInterface $transform)
    {
        return $transform->apply($this->dataSource);
    }
    /**
     * @param \Camspiers\StatisticalClassifier\Model\ModelInterface $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }
    /**
     * @param \Camspiers\StatisticalClassifier\DataSource\DataSourceInterface $dataSource
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }
    /**
     * Builds the model from the data source by applying transforms to the data source
     * @return null
     */
    abstract public function prepareModel();
}
