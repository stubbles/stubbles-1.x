<?php
/**
 * Test for net::stubbles::peer::stubSocket.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @version     $Id: stubSocketTestCase.php 2435 2010-01-04 22:10:32Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubSocket');
/**
 * Test for net::stubbles::peer::stubSocket.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @group       peer
 */
class stubSocketTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function values()
    {
        $socket = new stubSocket('example.com');
        $this->assertEquals('example.com', $socket->getHost());
        $this->assertEquals(80, $socket->getPort());
        $this->assertNull($socket->getPrefix());
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
    public function valuesWithSocketAndPrefix()
    {
        $socket = new stubSocket('example.com', 443, 'ssl://', 30);
        $this->assertEquals('example.com', $socket->getHost());
        $this->assertEquals(443, $socket->getPort());
        $this->assertEquals('ssl://', $socket->getPrefix());
        $this->assertEquals(30, $socket->getTimeout());
        $this->assertSame($socket, $socket->setTimeout(60));
        $this->assertEquals(60, $socket->getTimeout());
        $this->assertFalse($socket->isConnected());
        $this->assertTrue($socket->eof());
    }

    /**
     * assure that trying to read on an unconnected socket throws an IllegalStateException
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function readOnUnconnected()
    {    
        $socket = new stubSocket('example.com');
        $data = $socket->read();
    }

    /**
     * assure that trying to read on an unconnected socket throws an IllegalStateException
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function readLineOnUnconnected()
    {
        $socket = new stubSocket('example.com');
        $data = $socket->readLine();
    }

    /**
     * assure that trying to write on an unconnected socket throws an IllegalStateException
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function writeOnUnconnected()
    {
        $socket = new stubSocket('example.com');
        $data = $socket->write('data');
    }

    /**
     * @test
     */
    public function disconnectReturnsInstance()
    {
        $socket = new stubSocket('example.com');
        $this->assertSame($socket, $socket->disconnect());
    }
}
?>