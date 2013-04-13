<?php
namespace Camspiers\StatisticalClassifier\DataSource;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-26 at 18:06:09.
 */
class DataArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $object;
    /**
     * @var
     */
    protected $data;
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new DataArray(
            $this->data = array(
                array(
                    'category' => 'test',
                    'document' => 'Test'
                ),
                array(
                    'category' => 'test2',
                    'document' => 'Test'
                )
            )
        );
    }

    public function testGetCategories()
    {
        $this->assertEquals(
            array(
                'test',
                'test2'
            ),
            $this->object->getCategories()
        );
    }

    public function testHasCategory()
    {
        $this->assertTrue($this->object->hasCategory('test'));
        $this->assertTrue($this->object->hasCategory('test2'));
        $this->assertFalse($this->object->hasCategory('test3'));
    }

    public function testAddDocument()
    {
        $this->object->addDocument('test', 'Another');
        $this->assertEquals(
            array(
                array(
                    'document' => 'Test',
                    'category' => 'test'
                ),
                array(
                    'document' => 'Test',
                    'category' => 'test2'
                ),
                array(
                    'document' => 'Another',
                    'category' => 'test'
                )
            ),
            $this->object->getData()
        );
    }

    public function testGetData()
    {
        $this->assertEquals(
            $this->data,
            $this->object->getData()
        );
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage This data source cannot be written
     */
    public function testWrite()
    {
        $this->object->write();
    }

    public function testSerialize()
    {
        $this->assertEquals(
            serialize($this->data),
            $serialize = $this->object->serialize()
        );
        return $serialize;
    }

    /**
     * @depends testSerialize
     */
    public function testUnserialize($data)
    {
        $this->object->unserialize($data);
        $this->assertEquals(
            $this->data,
            $this->object->getData()
        );
    }
}
