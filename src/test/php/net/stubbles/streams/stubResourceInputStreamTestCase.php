<?php
/**
 * Test for net::stubbles::streams::stubResourceInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @version     $Id: stubResourceInputStreamTestCase.php 2101 2009-02-13 13:38:17Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubResourceInputStream');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  streams_test
 */
class TeststubResourceInputStream extends stubResourceInputStream
{
    /**
     * constructor
     *
     * @param   resource  $handle
     */
    public function __construct($handle)
    {
        $this->setHandle($handle);
    }
}
/**
 * Test for net::stubbles::streams::stubResourceInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @group       streams
 */
class stubResourceInputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubResourceInputStream
     */
    protected $resourceInputStream;
    /**
     * the handle
     *
     * @var  resource
     */
    protected $handle;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->handle              = fopen(dirname(__FILE__) . '/test_read.txt', 'r');
        $this->resourceInputStream = new TeststubResourceInputStream($this->handle);
    }

    /**
     * try to create an instance with an invalid handle
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidHandle()
    {
        $resourceInputStream = new TeststubResourceInputStream('invalid');
    }

    /**
     * read data from resource
     *
     * @test
     */
    public function read()
    {
        $this->assertEquals(13, $this->resourceInputStream->bytesLeft());
        $this->assertEquals("foobarbaz\njjj", $this->resourceInputStream->read());
        $this->assertEquals(0, $this->resourceInputStream->bytesLeft());
    }

    /**
     * read data from resource
     *
     * @test
     */
    public function readBytes()
    {
        $this->assertEquals(13, $this->resourceInputStream->bytesLeft());
        $this->assertEquals('foobar', $this->resourceInputStream->read(6));
        $this->assertEquals(7, $this->resourceInputStream->bytesLeft());
    }

    /**
     * read data from resource
     *
     * @test
     */
    public function readLine()
    {
        $this->assertEquals(13, $this->resourceInputStream->bytesLeft());
        $this->assertEquals('foobarbaz', $this->resourceInputStream->readLine());
        $this->assertEquals(3, $this->resourceInputStream->bytesLeft());
    }

    /**
     * check end of file pointer
     *
     * @test
     */
    public function endOfFile()
    {
        $this->assertFalse($this->resourceInputStream->eof());
        $this->resourceInputStream->read();
        $this->assertTrue($this->resourceInputStream->eof());
        $this->assertEquals('', $this->resourceInputStream->read());
        $this->assertEquals('', $this->resourceInputStream->readLine());
    }

    /**
     * check end of file pointer
     *
     * @test
     */
    public function endOfFileReadLine()
    {
        $this->assertFalse($this->resourceInputStream->eof());
        $this->assertEquals('foobarbaz', $this->resourceInputStream->readLine());
        $this->assertFalse($this->resourceInputStream->eof());
        $this->assertEquals('jjj', $this->resourceInputStream->readLine());
        $this->assertTrue($this->resourceInputStream->eof());
        $this->assertEquals('', $this->resourceInputStream->readLine());
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function readAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->read();
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function readLineAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->readLine();
    }

    /**
     * trying to ask for left bytes to read fails after resource was closed
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function bytesLeftAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->bytesLeft();
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  stubIOException
     */
    public function readAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->read();
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  stubIOException
     */
    public function readLineAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->readLine();
    }

    /**
     * trying to ask for left bytes to read fails after resource was closed
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function bytesLeftAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->bytesLeft();
    }
}
?>