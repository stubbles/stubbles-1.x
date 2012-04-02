<?php
/**
 * Abstract base class for decorated output streams.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubAbstractDecoratedOutputStream.php 2291 2009-08-20 20:14:44Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubDecoratedOutputStream');
/**
 * Abstract base class for decorated output streams.
 *
 * @package     stubbles
 * @subpackage  streams
 */
abstract class stubAbstractDecoratedOutputStream extends stubBaseObject implements stubDecoratedOutputStream
{
    /**
     * input stream to encode into internal encoding
     *
     * @var  stubOutputStream
     */
    protected $outputStream;

    /**
     * constructor
     *
     * @param  stubOutputStream  $outputStream
     */
    public function __construct(stubOutputStream $outputStream)
    {
        $this->outputStream = $outputStream;
    }

    /**
     * replace current enclosed output stream
     *
     * @param   stubOutputStream                   $outputStream
     * @return  stubAbstractDecoratedOutputStream
     */
    public function setEnclosedOutputStream(stubOutputStream $outputStream)
    {
        $this->outputStream = $outputStream;
        return $this;
    }

    /**
     * returns enclosed output stream
     *
     * @return  stubOutputStream
     */
    public function getEnclosedOutputStream()
    {
        return $this->outputStream;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        return $this->outputStream->write($bytes);
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        return $this->outputStream->writeLine($bytes);
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->outputStream->close();
    }
}
?>