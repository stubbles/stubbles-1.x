<?php
/**
 * Test for net::stubbles::peer::stubSocketOutputStream.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @version     $Id: stubSocketOutputStreamTestCase.php 2254 2009-06-23 20:38:41Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubSocketOutputStream');
/**
 * Test for net::stubbles::peer::stubSocketOutputStream.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @group       peer
 */
class stubSocketOutputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubSocketOutputStream
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
        $this->socketOutputStream = new stubSocketOutputStream($this->mockSocket);
    }

    /**
     * read() reads from socket with default length
     *
     * @test
     */
    public function readFromSocketWithDefaultLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('write')
                         ->with($this->equalTo('foo'))
                         ->will($this->returnValue(3));
        $this->assertEquals(3, $this->socketOutputStream->write('foo'));
    }

    /**
     * readLine() reads from socket with default length
     *
     * @test
     */
    public function readLineFromSocketWithDefaultLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('write')
                         ->with($this->equalTo("foo\r\n"))
                         ->will($this->returnValue(5));
        $this->assertEquals(5, $this->socketOutputStream->writeLine('foo'));
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
        $this->socketOutputStream->close();
    }
}
?>