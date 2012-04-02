<?php
/**
 * Input stream to read output of an executed command.
 *
 * @package     stubbles
 * @subpackage  console
 * @version     $Id: stubCommandInputStream.php 2165 2009-04-16 20:42:52Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubIllegalStateException',
                      'net::stubbles::lang::exceptions::stubIOException',
                      'net::stubbles::lang::exceptions::stubRuntimeException',
                      'net::stubbles::streams::stubResourceInputStream'
);
/**
 * Input stream to read output of an executed command.
 *
 * @package     stubbles
 * @subpackage  console
 */
class stubCommandInputStream extends stubResourceInputStream
{
    /**
     * original command
     *
     * @var  string
     */
    protected $command;

    /**
     * constructor
     *
     * @param   resource  $resource
     * @param   string    $command   optional
     * @throws  stubIllegalArgumentException
     */
    public function __construct($resource, $command = null)
    {
        if (is_resource($resource) === false || get_resource_type($resource) !== 'stream') {
            throw new stubIllegalArgumentException('Resource must be an already opened process resource.');
        }
        
        $this->setHandle($resource);
        $this->command = $command;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        try {
            $this->close();
        } catch (Exception $e) {
            // ignore exception
        }
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

        $data = @fgets($this->handle, $length);
        if (false === $data) {
            if (@feof($this->handle) === false) {
                throw new stubIOException('Can not read from input stream.');
            }
            
            return '';
        }

        return $data;
    }

    /**
     * closes the stream
     *
     * @throws  stubRuntimeException
     */
    public function close()
    {
        if (null !== $this->handle) {
            $returnCode   = pclose($this->handle);
            $this->handle = null;
            if (0 != $returnCode) {
                throw new stubRuntimeException('Executing command ' . $this->command . ' failed: #' . $returnCode);
            }
        }
    }
}
?>