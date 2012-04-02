<?php
/**
 * Factory for file streams.
 *
 * @package     stubbles
 * @subpackage  streams_file
 * @version     $Id: stubFileStreamFactory.php 2324 2009-09-16 11:50:14Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubStreamFactory',
                      'net::stubbles::streams::file::stubFileInputStream',
                      'net::stubbles::streams::file::stubFileOutputStream'
);
/**
 * Factory for file streams.
 *
 * @package     stubbles
 * @subpackage  streams_file
 */
class stubFileStreamFactory extends stubBaseObject implements stubStreamFactory
{
    /**
     * default file mode if directory for output stream should be created
     *
     * @var  int
     */
    protected $fileMode;

    /**
     * constructor
     *
     * @param  int  $fileMode  default file mode if directory for output stream should be created
     * @Inject(optional=true)
     * @Named('net.stubbles.filemode')
     */
    public function __construct($fileMode = 0700)
    {
        $this->fileMode = 0700;
    }

    /**
     * creates an input stream for given source
     *
     * @param   mixed                $source   source to create input stream from
     * @param   array<string,mixed>  $options  list of options for the input stream
     * @return  stubInputStream
     */
    public function createInputStream($source, array $options = array())
    {
        if (isset($options['filemode']) === true) {
            return new stubFileInputStream($source, $options['filemode']);
        }
        
        return new stubFileInputStream($source);
    }

    /**
     * creates an output stream for given target
     *
     * @param   mixed                $target   target to create output stream for
     * @param   array<string,mixed>  $options  list of options for the output stream
     * @return  stubOutputStream
     */
    public function createOutputStream($target, array $options = array())
    {
        if (isset($options['createDirIfNotExists']) === true && true === $options['createDirIfNotExists']) {
            $dir = dirname($target);
            if (file_exists($dir) === false) {
                $filemode = ((isset($options['dirPermissions']) === false) ? ($this->fileMode) : ($options['dirPermissions']));
                mkdir($dir, $filemode, true);
            }
        }
        
        $filemode = (isset($options['filemode']) === false) ? ('wb') : ($options['filemode']);
        $delayed  = (isset($options['delayed']) === false) ? (false) : ($options['delayed']);
        return new stubFileOutputStream($target, $filemode, $delayed);
    }
}
?>