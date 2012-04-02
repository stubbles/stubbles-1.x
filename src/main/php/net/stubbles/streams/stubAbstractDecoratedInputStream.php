<?php
/**
 * Abstract base class for decorated input streams.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubAbstractDecoratedInputStream.php 2291 2009-08-20 20:14:44Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubDecoratedInputStream');
/**
 * Abstract base class for decorated input streams.
 *
 * @package     stubbles
 * @subpackage  streams
 */
abstract class stubAbstractDecoratedInputStream extends stubBaseObject implements stubDecoratedInputStream
{
    /**
     * input stream to encode into internal encoding
     *
     * @var  stubInputStream
     */
    protected $inputStream;

    /**
     * constructor
     *
     * @param  stubInputStream  $inputStream
     */
    public function __construct(stubInputStream $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    /**
     * replace current enclosed input stream
     *
     * @param   stubInputStream                   $inputStream
     * @return  stubAbstractDecoratedInputStream
     */
    public function setEnclosedInputStream(stubInputStream $inputStream)
    {
        $this->inputStream = $inputStream;
        return $this;
    }

    /**
     * returns enclosed input stream
     *
     * @return  stubInputStream
     */
    public function getEnclosedInputStream()
    {
        return $this->inputStream;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        return $this->inputStream->read($length);
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        return $this->inputStream->readLine($length);
    }

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft()
    {
        return $this->inputStream->bytesLeft();
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->inputStream->eof();
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->inputStream->close();
    }
}
?>