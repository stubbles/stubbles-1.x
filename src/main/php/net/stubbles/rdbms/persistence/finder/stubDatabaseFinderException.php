<?php
/**
 * Exception to be thrown if a problem in the finder occurs.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_finder
 * @version     $Id: stubDatabaseFinderException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception to be thrown if a problem in the finder occurs.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_finder
 */
class stubDatabaseFinderException extends stubChainedException
{
    // intentionally empty
}
?>