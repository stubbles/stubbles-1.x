<?php
/**
 * Test for net::stubbles::streams::stubAbstractDecoratedInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @version     $Id: stubAbstractDecoratedInputStreamTestCase.php 2294 2009-08-20 20:43:15Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubAbstractDecoratedInputStream');
/**
 * Helper class for the test to make abstract class instantiable.
 *
 * @package     stubbles
 * @subpackage  streams_test
 */
class TeststubAbstractDecoratedInputStream extends stubAbstractDecoratedInputStream
{
    // intentionally empty
}
/**
 * Test for net::stubbles::streams::stubAbstractDecoratedInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @group       streams
 */
class stubAbstractDecoratedInputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAbstractDecoratedInputStream
     */
    protected $abstractDecoratedInputStream;
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
        $this->mockInputStream              = $this->getMock('stubInputStream');
        $this->abstractDecoratedInputStream = new TeststubAbstractDecoratedInputStream($this->mockInputStream);
    }

    /**
     * set() and get() enclosed input stream
     *
     * @test
     */
    public function setAndGetEnclosedInputStream()
    {
        $this->assertSame($this->mockInputStream, $this->abstractDecoratedInputStream->getEnclosedInputStream());
        $mockInputStream2 = $this->getMock('stubInputStream');
        $this->assertSame($this->abstractDecoratedInputStream,
                          $this->abstractDecoratedInputStream->setEnclosedInputStream($mockInputStream2)
        );
        $this->assertSame($mockInputStream2, $this->abstractDecoratedInputStream->getEnclosedInputStream());
    }

    /**
     * data returned from read() should be returned
     *
     * @test
     */
    public function read()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('read')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->abstractDecoratedInputStream->read());
    }

    /**
     * data returned from readLine() should be returned
     *
     * @test
     */
    public function readLine()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('readLine')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->abstractDecoratedInputStream->readLine());
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
        $this->assertEquals(5, $this->abstractDecoratedInputStream->bytesLeft());
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
        $this->assertFalse($this->abstractDecoratedInputStream->eof());
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
        $this->abstractDecoratedInputStream->close();
    }
}
?>