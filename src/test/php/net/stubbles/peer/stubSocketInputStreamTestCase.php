<?php
/**
 * Test for net::stubbles::peer::stubSocketInputStream.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @version     $Id: stubSocketInputStreamTestCase.php 2254 2009-06-23 20:38:41Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubSocketInputStream');
/**
 * Test for net::stubbles::peer::stubSocketInputStream.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @group       peer
 */
class stubSocketInputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubSocketInputStream
     */
    protected $socketInputStream;
    /**
     * mocked socket instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */ 
    protected $mockSocket;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockSocket        = $this->getMock('stubSocket', array(), array('example.com'));
        $this->mockSocket->expects($this->once())
                         ->method('connect');
        $this->socketInputStream = new stubSocketInputStream($this->mockSocket);
    }

    /**
     * read() reads from socket with default length
     *
     * @test
     */
    public function readFromSocketWithDefaultLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('read')
                         ->with($this->equalTo(8192))
                         ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->socketInputStream->read());
    }

    /**
     * read() reads from socket with given length
     *
     * @test
     */
    public function readFromSocketWithGivenLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('read')
                         ->with($this->equalTo(3))
                         ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->socketInputStream->read(3));
    }

    /**
     * readLine() reads from socket with default length
     *
     * @test
     */
    public function readLineFromSocketWithDefaultLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('readLine')
                         ->with($this->equalTo(8192))
                         ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->socketInputStream->readLine());
    }

    /**
     * readLine() reads from socket with given length
     *
     * @test
     */
    public function readLineFromSocketWithGivenLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('readLine')
                         ->with($this->equalTo(3))
                         ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->socketInputStream->readLine(3));
    }

    /**
     * socket at end means no bytes left
     *
     * @test
     */
    public function noBytesLeft()
    {
        $this->mockSocket->expects($this->exactly(2))
                         ->method('eof')
                         ->will($this->returnValue(true));
        $this->assertEquals(-1, $this->socketInputStream->bytesLeft());
        $this->assertTrue($this->socketInputStream->eof());
    }

    /**
     * socket not at end means at least one byte left
     *
     * @test
     */
    public function bytesLeft()
    {
        $this->mockSocket->expects($this->exactly(2))
                         ->method('eof')
                         ->will($this->returnValue(false));
        $this->assertEquals(1, $this->socketInputStream->bytesLeft());
        $this->assertFalse($this->socketInputStream->eof());
    }

    /**
     * closing the stream disconnects the socket
     *
     * @test
     */
    public function closingTheStreamDisconnectsTheSocket()
    {
        $this->mockSocket->expects($this->atLeastOnce())
                         ->method('disconnect');
        $this->socketInputStream->close();
    }
}
?>