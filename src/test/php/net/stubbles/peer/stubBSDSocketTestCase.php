<?php
/**
 * Test for net::stubbles::peer::stubBSDSocket.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @version     $Id: stubBSDSocketTestCase.php 2435 2010-01-04 22:10:32Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubBSDSocket');
/**
 * Test for net::stubbles::peer::stubBSDSocket.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @group       peer
 */
class stubBSDSocketTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function values()
    {
        $socket = new stubBSDSocket('example.com');
        $this->assertEquals('example.com', $socket->getHost());
        $this->assertEquals(80, $socket->getPort());
        $this->assertEquals(5, $socket->getTimeout());
        $this->assertSame($socket, $socket->setTimeout(60));
        $this->assertEquals(60, $socket->getTimeout());
        $this->assertFalse($socket->isConnected());
        $this->assertTrue($socket->eof());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valuesWithPort()
    {
        $socket = new stubBSDSocket('example.com', 21, null, 30);
        $this->assertEquals('example.com', $socket->getHost());
        $this->assertEquals(21, $socket->getPort());
        $this->assertEquals(30, $socket->getTimeout());
        $this->assertSame($socket, $socket->setTimeout(60));
        $this->assertEquals(60, $socket->getTimeout());
        $this->assertFalse($socket->isConnected());
        $this->assertTrue($socket->eof());
    }

    /**
     * domain property can be set and get
     *
     * @test
     */
    public function domainProperty()
    {
        $socket = new stubBSDSocket('example.com');
        $this->assertEquals(AF_INET, $socket->getDomain());
        $this->assertSame($socket, $socket->setDomain(AF_INET6));
        $this->assertEquals(AF_INET6, $socket->getDomain());
        $this->assertSame($socket, $socket->setDomain(AF_UNIX));
        $this->assertEquals(AF_UNIX, $socket->getDomain());
        $this->assertSame($socket, $socket->setDomain(AF_INET));
        $this->assertEquals(AF_INET, $socket->getDomain());
    }

    /**
     * trying to set an invalid domain throws an illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidDomainThrowsIllegalArgumentException()
    {
        $socket = new stubBSDSocket('example.com');
        $socket->setDomain('invalid');
    }

    /**
     * trying to set the domain when connected throws an illegal state exception
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function setDomainWhenConnectedThrowsIllegalStateConnection()
    {
        $socket = $this->getMock('stubBSDSocket', array('isConnected', 'disconnect'), array('example.com'));
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->setDomain(AF_UNIX);
    }

    /**
     * type property can be set and get
     *
     * @test
     */
    public function typeProperty()
    {
        $socket = new stubBSDSocket('example.com');
        $this->assertEquals(SOCK_STREAM, $socket->getType());
        $this->assertSame($socket, $socket->setType(SOCK_DGRAM));
        $this->assertEquals(SOCK_DGRAM, $socket->getType());
        $this->assertSame($socket, $socket->setType(SOCK_RAW));
        $this->assertEquals(SOCK_RAW, $socket->getType());
        $this->assertSame($socket, $socket->setType(SOCK_SEQPACKET));
        $this->assertEquals(SOCK_SEQPACKET, $socket->getType());
        $this->assertSame($socket, $socket->setType(SOCK_RDM));
        $this->assertEquals(SOCK_RDM, $socket->getType());
        $this->assertSame($socket, $socket->setType(SOCK_STREAM));
        $this->assertEquals(SOCK_STREAM, $socket->getType());
    }

    /**
     * trying to set an invalid type throws an illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidTypeThrowsIllegalArgumentException()
    {
        $socket = new stubBSDSocket('example.com');
        $socket->setType('invalid');
    }

    /**
     * trying to set the type when connected throws an illegal state exception
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function setTypeWhenConnectedThrowsIllegalStateConnection()
    {
        $socket = $this->getMock('stubBSDSocket', array('isConnected', 'disconnect'), array('example.com'));
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->setType(SOCK_SEQPACKET);
    }

    /**
     * protocol property can be set and get
     *
     * @test
     */
    public function protocolProperty()
    {
        $socket = new stubBSDSocket('example.com');
        $this->assertEquals(SOL_TCP, $socket->getProtocol());
        $this->assertSame($socket, $socket->setProtocol(SOL_UDP));
        $this->assertEquals(SOL_UDP, $socket->getProtocol());
        $this->assertSame($socket, $socket->setProtocol(SOL_TCP));
        $this->assertEquals(SOL_TCP, $socket->getProtocol());
    }

    /**
     * trying to set an invalid protocol throws an illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidProtocolThrowsIllegalArgumentException()
    {
        $socket = new stubBSDSocket('example.com');
        $socket->setProtocol('invalid');
    }

    /**
     * trying to set the protocol when connected throws an illegal state exception
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function setProtocolWhenConnectedThrowsIllegalStateConnection()
    {
        $socket = $this->getMock('stubBSDSocket', array('isConnected', 'disconnect'), array('example.com'));
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->setProtocol(SOL_UDP);
    }

    /**
     * ensure options are set and get even when not connected
     *
     * @test
     */
    public function optionHandling()
    {
        $socket = new stubBSDSocket('example.com');
        $this->assertSame($socket, $socket->setOption('foo', 'bar', 'baz'));
        $this->assertNull($socket->getOption('bar', 'baz'));
        $this->assertNull($socket->getOption('foo', 'baz'));
        $this->assertEquals('baz', $socket->getOption('foo', 'bar'));
    }

    /**
     * assure a normal read is done
     *
     * @test
     */
    public function readOnConnected()
    {    
        $socket = $this->getMock('stubBSDSocket', array('isConnected', 'disconnect', 'doRead'), array('example.com'));
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->expects($this->once())
               ->method('doRead')
               ->with($this->equalTo(4096), $this->equalTo(PHP_NORMAL_READ))
               ->will($this->returnValue("foo\n"));
        $this->assertEquals("foo\n", $socket->read());
    }

    /**
     * assure that trying to read on an unconnected socket throws an IllegalStateException
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function readOnUnconnected()
    {    
        $socket = new stubBSDSocket('example.com');
        $data   = $socket->read();
    }

    /**
     * assure a normal read is done
     *
     * @test
     */
    public function readLineOnConnected()
    {    
        $socket = $this->getMock('stubBSDSocket', array('isConnected', 'disconnect', 'doRead'), array('example.com'));
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->expects($this->once())
               ->method('doRead')
               ->with($this->equalTo(4096), $this->equalTo(PHP_NORMAL_READ))
               ->will($this->returnValue("foo\n"));
        $this->assertEquals('foo', $socket->readLine());
    }

    /**
     * assure that trying to read on an unconnected socket throws an IllegalStateException
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function readLineOnUnconnected()
    {
        $socket = new stubBSDSocket('example.com');
        $data   = $socket->readLine();
    }

    /**
     * assure a binary read is done
     *
     * @test
     */
    public function readBinaryOnConnected()
    {    
        $socket = $this->getMock('stubBSDSocket', array('isConnected', 'disconnect', 'doRead'), array('example.com'));
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->expects($this->once())
               ->method('doRead')
               ->with($this->equalTo(1024), $this->equalTo(PHP_BINARY_READ))
               ->will($this->returnValue("foo\n"));
        $this->assertEquals("foo\n", $socket->readBinary());
    }

    /**
     * assure that trying to read on an unconnected socket throws an IllegalStateException
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function readBinaryOnUnconnected()
    {
        $socket = new stubBSDSocket('example.com');
        $data   = $socket->readBinary();
    }

    /**
     * assure that trying to write on an unconnected socket throws an IllegalStateException
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function writeOnUnconnected()
    {
        $socket = new stubBSDSocket('example.com');
        $socket->write('data');
    }

    /**
     * @test
     */
    public function disconnectReturnsInstance()
    {
        $socket = new stubBSDSocket('example.com');
        $this->assertSame($socket, $socket->disconnect());
    }
}
?>