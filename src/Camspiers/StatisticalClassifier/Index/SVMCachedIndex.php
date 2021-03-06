<?php

namespace Camspiers\StatisticalClassifier\Index;

use CacheCache\Cache;
use Camspiers\StatisticalClassifier\DataSource\DataSourceInterface;

class SVMCachedIndex extends CachedIndex
{
    /**
     * @param string              $modelFilename
     * @param string              $indexName
     * @param Cache               $cache
     * @param DataSourceInterface $dataSource
     */
    public function __construct(
        $modelFilename,
        $indexName,
        Cache $cache,
        DataSourceInterface $dataSource = null
    ) {
        $this->modelFilename = $modelFilename;
        parent::__construct(
            $indexName,
            $cache,
            $dataSource
        );
    }
    /**
     * Save the index to the cache
     * @return null
     */
    public function preserve()
    {
        parent::preserve();
        foreach ($this->partitions as $partition) {
            if ($partition instanceof \SVMModel) {
                $partition->save($this->modelFilename);
            }
        }
    }
    /**
     * Restore the index from the cache
     * @return null
     */
    protected function restore()
    {
        parent::restore();
        if (file_exists($this->modelFilename)) {
            foreach ($this->partitions as $partition) {
                if ($partition instanceof \SVMModel) {
                    $partition->load($this->modelFilename);
                }
            }
        }
    }
}
