<?php
/**
 * Test for net::stubbles::streams::memory::stubMemoryOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_memory_test
 * @version     $Id: stubMemoryOutputStreamTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::memory::stubMemoryOutputStream');
/**
 * Test for net::stubbles::streams::memory::stubMemoryOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_memory_test
 * @group       streams
 * @group       streams_memory
 */
class stubMemoryOutputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * the file url used in the tests
     *
     * @var  stubMemoryOutputStream
     */
    protected $memoryOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryOutputStream = new stubMemoryOutputStream();
    }

    /**
     * write() puts data into buffer
     *
     * @test
     */
    public function write()
    {
        $this->assertEquals('', $this->memoryOutputStream->getBuffer());
        $this->assertEquals(5, $this->memoryOutputStream->write('hello'));
        $this->assertEquals('hello', $this->memoryOutputStream->getBuffer());
    }

    /**
     * writeLine() puts data into buffer
     *
     * @test
     */
    public function writeLine()
    {
        $this->assertEquals('', $this->memoryOutputStream->getBuffer());
        $this->assertEquals(6, $this->memoryOutputStream->writeLine('hello'));
        $this->assertEquals("hello\n", $this->memoryOutputStream->getBuffer());
    }

    /**
     * close() does nothing
     *
     * @test
     */
    public function close()
    {
        $this->memoryOutputStream->close();
    }
}
?>