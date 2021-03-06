<?php

/**
 * This file is part of the Statistical Classifier package.
 *
 * (c) Cam Spiers <camspiers@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Camspiers\StatisticalClassifier\Index;

use Camspiers\StatisticalClassifier\DataSource\DataSourceInterface;

/**
 * @author  Cam Spiers <camspiers@gmail.com>
 * @package Statistical Classifier
 */
interface IndexInterface
{
    /**
     * Returns whether or not the index is prepared
     * @return boolean The prepared status
     */
    public function isPrepared();
    /**
     * Set the index to prepared
     * @param boolean $prepared The prepared status
     */
    public function setPrepared($prepared);
    /**
     * Set a data source for the index
     * @param DataSourceInterface $dataSource The data source
     */
    public function setDataSource(DataSourceInterface $dataSource);
    /**
     * Get the datasource if one exists
     * @return DataSourceInterface|null The data source
     */
    public function getDataSource();
    /**
     * Return a partition if it exists
     * @param  string $partitionName The name of the partitiion
     * @return mixed  The partition data
     */
    public function getPartition($partitionName);
    /**
     * Set a partition to the index
     * @param  string $partitionName The name of the partitiion
     * @param  string $partition     The partition data
     * @return null
     */
    public function setPartition($partitionName, $partition);
    /**
     * Removes a partition if it exists
     * @param  string $partitionName The name of the partition
     * @return null
     */
    public function removePartition($partitionName);
    /**
     * Return all partitions names
     * @return array
     */
    public function getPartitions();
}
