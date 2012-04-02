<?php
/**
 * Class for file based output streams.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubFileOutputStream.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubResourceOutputStream',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubIOException'
);
/**
 * Class for file based output streams.
 *
 * @package     stubbles
 * @subpackage  streams
 */
class stubFileOutputStream extends stubResourceOutputStream
{
    /**
     * name of file
     *
     * @var  string
     */
    protected $file;
    /**
     * opening mode
     *
     * @var  string
     */
    protected $mode;

    /**
     * constructor
     *
     * The delayed param only works in conjunction with the $file param being a
     * string. If set to true and the file does not exist creation of the file
     * will be delayed until first bytes should be written to the output stream.
     *
     * @param   string|resource  $file
     * @param   string           $mode     optional  opening mode if $file is a filename
     * @param   bool             $delayed  optional
     * @throws  stubIllegalArgumentException
     */
    public function __construct($file, $mode = 'wb', $delayed = false)
    {
        if (is_string($file) === true) {
            if (false === $delayed) {
                $this->setHandle($this->openFile($file, $mode));
            } else {
                $this->file = $file;
                $this->mode = $mode;
            }
        } elseif (is_resource($file) === true && get_resource_type($file) === 'stream') {
            $this->setHandle($file);
        } else {
            throw new stubIllegalArgumentException('File must either be a filename or an already opened file/stream resource.');
        }
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        // delayed file creation?
        if (null === $this->handle && null != $this->file) {
            $this->setHandle($this->openFile($this->file, $this->mode));
        }
        
        return parent::write($bytes);
    }

    /**
     * helper method to open a file handle
     *
     * @param   string            $file
     * @param   string            $mode
     * @return  resource<stream>
     * @throws  stubIOException
     */
    protected function openFile($file, $mode)
    {
        $fp = @fopen($file, $mode);
        if (false === $fp) {
            throw new stubIOException('Can not open file ' . $file . ' with mode ' . $mode);
        }
        
        return $fp;
    }
}
?>