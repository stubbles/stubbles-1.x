<?php
/**
 * Input stream for reading sockets.
 *
 * @package     stubbles
 * @subpackage  peer
 * @version     $Id: stubSocketInputStream.php 2254 2009-06-23 20:38:41Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubSocket',
                      'net::stubbles::streams::stubInputStream'
);
/**
 * Input stream for reading sockets.
 *
 * @package     stubbles
 * @subpackage  peer
 */
class stubSocketInputStream extends stubBaseObject implements stubInputStream
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
     * reads given amount of bytes
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        return $this->socket->read($length);
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        return $this->socket->readLine($length);
    }

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft()
    {
        if ($this->socket->eof() === true) {
            return -1;
        }
        
        return 1;
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->socket->eof();
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