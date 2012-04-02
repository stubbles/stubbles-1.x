<?php
/**
 * Exception to be thrown in case a method is called which is not supported by
 * a specific implementation.
 * 
 * @package     stubbles
 * @subpackage  lang_exceptions
 * @version     $Id: stubMethodNotSupportedException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubRuntimeException');
/**
 * Exception to be thrown in case a method is called which is not supported by
 * a specific implementation.
 * 
 * @package     stubbles
 * @subpackage  lang_exceptions
 */
class stubMethodNotSupportedException extends stubRuntimeException
{
    // intentionally empty
}
?>