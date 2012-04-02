<?php
/**
 * Class for file based input streams.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubFileInputStream.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubIllegalStateException',
                      'net::stubbles::lang::exceptions::stubIOException',
                      'net::stubbles::streams::stubResourceInputStream',
                      'net::stubbles::streams::stubSeekable'
);
/**
 * Class for file based input streams.
 *
 * @package     stubbles
 * @subpackage  streams
 */
class stubFileInputStream extends stubResourceInputStream implements stubSeekable
{
    /**
     * name of the file
     *
     * @var  string
     */
    protected $fileName;

    /**
     * constructor
     *
     * @param   string|resource  $file
     * @param   string           $mode  option  opening mode if $file is a filename
     * @throws  stubIOException
     * @throws  stubIllegalArgumentException
     */
    public function __construct($file, $mode = 'rb')
    {
        if (is_string($file) === true) {
            $fp = @fopen($file, $mode);
            if (false === $fp) {
                throw new stubIOException('Can not open file ' . $file . ' with mode ' . $mode);
            }
            
            $this->fileName = $file;
        } elseif (is_resource($file) === true && get_resource_type($file) === 'stream') {
            $fp = $file;
        } else {
            throw new stubIllegalArgumentException('File must either be a filename or an already opened file/stream resource.');
        }
        
        $this->setHandle($fp);
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->close();
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
        if (null === $this->fileName) {
            return parent::getResourceLength();
        }
        
        if (substr($this->fileName, 0, 16) === 'compress.zlib://') {
            return filesize(substr($this->fileName, 16));
        } elseif (substr($this->fileName, 0, 17) === 'compress.bzip2://') {
            return filesize(substr($this->fileName, 17));
        }
    }

    /**
     * seek to given offset
     *
     * @param   int  $offset
     * @param   int  $whence  one of stubSeekable::SET, stubSeekable::CURRENT or stubSeekable::END
     * @throws  stubIllegalStateException
     */
    public function seek($offset, $whence = stubSeekable::SET)
    {
        if (null === $this->handle) {
            throw new stubIllegalStateException('Can not read from closed input stream.');
        }

        fseek($this->handle, $offset, $whence);
    }

    /**
     * return current position
     *
     * @return  int
     * @throws  stubIllegalStateException
     * @throws  stubIOException
     */
    public function tell()
    {
        if (null === $this->handle) {
            throw new stubIllegalStateException('Can not read from closed input stream.');
        }

        $position = ftell($this->handle);
        if (false === $position) {
            throw new stubIOException('Can not read current position in file.');
        }

        return $position;
    }
}
?>