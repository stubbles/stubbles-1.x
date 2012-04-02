<?php
/**
 * Class to stream data from memory.
 *
 * @package     stubbles
 * @subpackage  streams_memory
 * @version     $Id: stubMemoryInputStream.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::streams::stubInputStream',
                      'net::stubbles::streams::stubSeekable'
);
/**
 * Class to stream data from memory.
 *
 * @package     stubbles
 * @subpackage  streams_memory
 */
class stubMemoryInputStream extends stubBaseObject implements stubInputStream, stubSeekable
{
    /**
     * written data
     *
     * @var  string
     */
    protected $buffer   = '';
    /**
     * current position in buffer
     *
     * @var  int
     */
    protected $position = 0;

    /**
     * constructor
     *
     * @param  string  $buffer
     */
    public function __construct($buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        $bytes           = substr($this->buffer, $this->position, $length);
        $this->position += strlen($bytes);
        return $bytes;
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        $bytes        = substr($this->buffer, $this->position, $length);
        $linebreakpos = strpos($bytes, "\n");
        if (false !== $linebreakpos) {
            $line = substr($bytes, 0, $linebreakpos);
            $this->position += strlen($line) + 1;
        } else {
            $line = $bytes;
            $this->position += strlen($line);
        }
        
        return $line;
    }

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft()
    {
        return strlen($this->buffer) - $this->position;
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        return (strlen($this->buffer) === $this->position);
    }

    /**
     * closes the stream
     */
    public function close()
    {
        // intentionally empty
    }

    /**
     * seek to given offset
     *
     * @param   int  $offset  new position or amount of bytes to seek
     * @param   int  $whence  optional  one of stubSeekable::SET, stubSeekable::CURRENT or stubSeekable::END
     * @throws  stubIllegalArgumentException
     */
    public function seek($offset, $whence = stubSeekable::SET)
    {
        switch ($whence) {
            case stubSeekable::SET:
                $this->position = $offset;
                break;
            
            case stubSeekable::CURRENT:
                $this->position += $offset;
                break;
            
            case stubSeekable::END:
                $this->position = strlen($this->buffer) + $offset;
                break;
            
            default:
                throw new stubIllegalArgumentException('Wrong value for $whence, must be one of stubSeekable::SET, stubSeekable::CURRENT or stubSeekable::END.');
        }
    }

    /**
     * return current position
     *
     * @return  int
     */
    public function tell()
    {
        return $this->position;
    }
}
?>