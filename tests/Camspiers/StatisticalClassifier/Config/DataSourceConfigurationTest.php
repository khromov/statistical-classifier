<?php

namespace Camspiers\StatisticalClassifier\Config;

use Symfony\Component\Config\Definition\Processor;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-26 at 18:06:08.
 */
class DataSourceConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected $processor;
    protected $config;

    protected function setUp()
    {
        $this->processor = new Processor();
        $this->config = new DataSourceConfiguration();
    }

    protected function tearDown()
    {
        $this->config = null;
    }

    public function testGetConfigTreeBuilder()
    {
        $result = $this->processor->processConfiguration(
            $this->config,
            array(
                $config = array(
                    array(
                        'category' => 'cat1',
                        'document' => 'doc1'
                    ),
                    array(
                        'category' => 'cat1',
                        'document' => 'doc2'
                    ),
                    array(
                        'category' => 'cat2',
                        'document' => 'doc1'
                    ),
                    array(
                        'category' => 'cat2',
                        'document' => 'doc2'
                    )
                )
            )
        );

        $this->assertEquals(
            $config,
            $result
        );
    }
}
