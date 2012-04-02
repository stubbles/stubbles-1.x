<?php
/**
 * Class to stream data into memory.
 *
 * @package     stubbles
 * @subpackage  streams_memory
 * @version     $Id: stubMemoryOutputStream.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubOutputStream');
/**
 * Class to stream data into memory.
 *
 * @package     stubbles
 * @subpackage  streams_memory
 */
class stubMemoryOutputStream extends stubBaseObject implements stubOutputStream
{
    /**
     * written data
     *
     * @var  string
     */
    protected $buffer = '';

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        $this->buffer .= $bytes;
        return strlen($bytes);
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        return $this->write($bytes . "\n");
    }

    /**
     * closes the stream
     */
    public function close()
    {
        // intentionally empty
    }

    /**
     * returns written contents
     *
     * @return  string
     */
    public function getBuffer()
    {
        return $this->buffer;
    }
}
?>