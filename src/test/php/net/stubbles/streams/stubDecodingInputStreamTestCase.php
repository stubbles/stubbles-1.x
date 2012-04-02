<?php
/**
 * Test for net::stubbles::streams::stubDecodingInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @version     $Id: stubDecodingInputStreamTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubDecodingInputStream');
/**
 * Test for net::stubbles::streams::stubDecodingInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @group       streams
 */
class stubDecodingInputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDecodingInputStream
     */
    protected $decodingInputStream;
    /**
     * mocked input stream
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInputStream     = $this->getMock('stubInputStream');
        $this->decodingInputStream = new stubDecodingInputStream($this->mockInputStream, 'iso-8859-1');
    }

    /**
     * data returned from read() should be decoded to UTF-8
     *
     * @test
     */
    public function read()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('read')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue(utf8_decode('hällö')));
        $this->assertEquals('hällö', $this->decodingInputStream->read());
    }

    /**
     * data returned from readLine() should be decoded to UTF-8
     *
     * @test
     */
    public function readLine()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('readLine')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue(utf8_decode('hällö')));
        $this->assertEquals('hällö', $this->decodingInputStream->readLine());
    }

    /**
     * data returned from bytesLeft() should be returned
     *
     * @test
     */
    public function bytesLeft()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('bytesLeft')
                              ->will($this->returnValue(5));
        $this->assertEquals(5, $this->decodingInputStream->bytesLeft());
    }

    /**
     * data returned from eof() should be returned
     *
     * @test
     */
    public function eof()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('eof')
                              ->will($this->returnValue(false));
        $this->assertFalse($this->decodingInputStream->eof());
    }

    /**
     * close() should close the inner input stream
     *
     * @test
     */
    public function close()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('close');
        $this->decodingInputStream->close();
    }
}
?>