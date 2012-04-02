<?php
/**
 * Exception to be thrown in case the variant configuration contains an error.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubVariantConfigurationException.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception to be thrown in case the variant configuration contains an error.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 */
class stubVariantConfigurationException extends stubChainedException
{
    // intentionally empty
}
?>