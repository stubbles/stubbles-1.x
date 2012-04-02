<?php
/**
 * Class for resource based output streams.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubResourceOutputStream.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubOutputStream',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubIllegalStateException',
                      'net::stubbles::lang::exceptions::stubIOException'
);
/**
 * Class for resource based output streams.
 *
 * @package     stubbles
 * @subpackage  streams
 */
abstract class stubResourceOutputStream extends stubBaseObject implements stubOutputStream
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
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     * @throws  stubIllegalStateException
     * @throws  stubIOException
     */
    public function write($bytes)
    {
        if (null === $this->handle) {
            throw new stubIllegalStateException('Can not write to closed output stream.');
        }
        
        $length = @fwrite($this->handle, $bytes);
        if (false === $length) {
            throw new stubIOException('Can not write to output stream.');
        }
        
        return $length;
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written
     */
    public function writeLine($bytes)
    {
        return $this->write($bytes . "\r\n");
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