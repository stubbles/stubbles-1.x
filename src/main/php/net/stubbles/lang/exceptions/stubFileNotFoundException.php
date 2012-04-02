<?php
/**
 * Exception to be thrown in case a file could not be found.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions
 * @version     $Id: stubFileNotFoundException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIOException');
/**
 * Exception to be thrown in case a file could not be found.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions
 */
class stubFileNotFoundException extends stubIOException
{
    /**
     * constructor
     *
     * @param  string  $fileName  name of file that was not found
     */
    public function __construct($fileName)
    {
        $this->message = "The file {$fileName} could not be found or is not readable.";
    }
}
?>