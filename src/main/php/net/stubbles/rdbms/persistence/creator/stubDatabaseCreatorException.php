<?php
/**
 * Exception to be thrown if a problem in the creator occurs.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_creator
 * @version     $Id: stubDatabaseCreatorException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception to be thrown if a problem in the creator occurs.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_creator
 */
class stubDatabaseCreatorException extends stubChainedException
{
    // intentionally empty
}
?>