<?php
/**
 * Test for net::stubbles::streams::stubEncodingOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @version     $Id: stubEncodingOutputStreamTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubEncodingOutputStream');
/**
 * Test for net::stubbles::streams::stubEncodingOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @group       streams
 */
class stubEncodingOutputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubEncodingOutputStream
     */
    protected $encodingOutputStream;
    /**
     * mocked input stream
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockOutputStream     = $this->getMock('stubOutputStream');
        $this->encodingOutputStream = new stubEncodingOutputStream($this->mockOutputStream, 'iso-8859-1');
    }

    /**
     * data send write() should be encoded to charset
     *
     * @test
     */
    public function write()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('write')
                               ->with($this->equalTo(utf8_decode('hällö')))
                               ->will($this->returnValue(5));
        $this->assertEquals(5, $this->encodingOutputStream->write('hällö'));
    }

    /**
     * data send writeLine() should be encoded to charset
     *
     * @test
     */
    public function writeLine()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo(utf8_decode('hällö')))
                               ->will($this->returnValue(6));
        $this->assertEquals(6, $this->encodingOutputStream->writeLine('hällö'));
    }

    /**
     * close() should close the inner output stream
     *
     * @test
     */
    public function close()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('close');
        $this->encodingOutputStream->close();
    }
}
?>