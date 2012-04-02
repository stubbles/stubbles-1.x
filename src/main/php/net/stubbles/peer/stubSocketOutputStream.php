<?php
/**
 * Output stream for writing to sockets.
 *
 * @package     stubbles
 * @subpackage  peer
 * @version     $Id: stubSocketOutputStream.php 2254 2009-06-23 20:38:41Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubSocket',
                      'net::stubbles::streams::stubOutputStream'
);
/**
 * Output stream for writing to sockets.
 *
 * @package     stubbles
 * @subpackage  peer
 */
class stubSocketOutputStream extends stubBaseObject implements stubOutputStream
{
    /**
     * socket to read from
     *
     * @var  stubSocket
     */
    protected $socket;

    /**
     * constructor
     *
     * @param  stubSocket  $socket
     */
    public function __construct(stubSocket $socket)
    {
        $this->socket = $socket;
        $this->socket->connect();
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        return $this->socket->write($bytes);
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        return $this->socket->write($bytes . "\r\n");
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->socket->disconnect();
    }
}
?>