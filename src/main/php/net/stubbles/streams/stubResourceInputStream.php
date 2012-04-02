<?php
/**
 * Class for resource based input streams.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubResourceInputStream.php 2101 2009-02-13 13:38:17Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubInputStream',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubIllegalStateException',
                      'net::stubbles::lang::exceptions::stubIOException'
);
/**
 * Class for resource based input streams.
 *
 * @package     stubbles
 * @subpackage  streams
 */
abstract class stubResourceInputStream extends stubBaseObject implements stubInputStream
{
    /**
     * the descriptor for the stream
     *
     * @var  int
     */
    protected $handle;

    /**
     * sets the resource to be used
     *
     * @param   resource  $handle
     * @throws  stubIllegalArgumentException
     */
    protected function setHandle($handle)
    {
        if (is_resource($handle) === false) {
            throw new stubIllegalArgumentException('Handle needs to be a stream resource.');
        }

        $this->handle = $handle;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     * @throws  stubIllegalStateException
     * @throws  stubIOException
     */
    public function read($length = 8192)
    {
        if (null === $this->handle) {
            throw new stubIllegalStateException('Can not read from closed input stream.');
        }

        $data = @fread($this->handle, $length);
        if (false === $data) {
            if (@feof($this->handle) === false) {
                throw new stubIOException('Can not read from input stream.');
            }
            
            return '';
        }

        return $data;
    }

    /**
     * reads given amount of bytes or until next line break and removes line break
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     * @throws  stubIllegalStateException
     * @throws  stubIOException
     */
    public function readLine($length = 8192)
    {
        if (null === $this->handle) {
            throw new stubIllegalStateException('Can not read from closed input stream.');
        }

        $data = @fgets($this->handle, $length);
        if (false === $data) {
            if (@feof($this->handle) === false) {
                throw new stubIOException('Can not read from input stream.');
            }
            
            return '';
        }

        return rtrim($data, "\r\n");
    }

    /**
     * returns the amount of bytes left to be read
     *
     * @return  int
     * @throws  stubIllegalStateException
     */
    public function bytesLeft()
    {
        if (null === $this->handle || is_resource($this->handle) === false) {
            throw new stubIllegalStateException('Can not read from closed input stream.');
        }

        $bytesRead = ftell($this->handle);
        if (is_int($bytesRead) === false) {
            return 0;
        }

        return $this->getResourceLength() - $bytesRead;
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        return feof($this->handle);
    }

    /**
     * helper method to retrieve the length of the resource
     *
     * Not all stream wrappers support (f)stat - the extending class then
     * needs to take care to deliver the correct resource length then.
     *
     * @return  int
     */
    protected function getResourceLength()
    {
        $fileData = fstat($this->handle);
        return $fileData['size'];
    }

    /**
     * closes the stream
     */
    public function close()
    {
        if (null !== $this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
?>