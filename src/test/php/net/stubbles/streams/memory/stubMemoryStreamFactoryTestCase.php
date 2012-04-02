<?php
/**
 * Test for net::stubbles::streams::memory::stubFileStreamFactory.
 *
 * @package     stubbles
 * @subpackage  streams_memory_test
 * @version     $Id: stubMemoryStreamFactoryTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::memory::stubMemoryStreamFactory');
/**
 * Test for net::stubbles::streams::memory::stubFileStreamFactory.
 *
 * @package     stubbles
 * @subpackage  streams_memory_test
 * @group       streams
 * @group       streams_memory
 */
class stubMemoryStreamFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMemoryStreamFactory
     */
    protected $memoryStreamFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryStreamFactory = new stubMemoryStreamFactory();
    }

    /**
     * @test
     */
    public function createInputStream()
    {
        $memoryInputStream = $this->memoryStreamFactory->createInputStream('buffer');
        $this->assertInstanceOf('stubMemoryInputStream', $memoryInputStream);
        $this->assertEquals('buffer', $memoryInputStream->readLine());
    }

    /**
     * @test
     */
    public function createOutputStream()
    {
        $this->assertInstanceOf('stubMemoryOutputStream', $this->memoryStreamFactory->createOutputStream('buffer'));
    }
}
?>