<?php
/**
 * Exception to be thrown in case any component has not been configured correctly
 *
 * @package     stubbles
 * @subpackage  lang_exceptions
 * @version     $Id: stubConfigurationException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception to be thrown in case any component has not been configured correctly
 *
 * @package     stubbles
 * @subpackage  lang_exceptions
 */
class stubConfigurationException extends stubChainedException
{
    // nothing to do
}
?>