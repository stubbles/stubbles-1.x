<?php
/**
 * Exception for general database problems.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @version     $Id: stubDatabaseException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception for general database problems.
 *
 * @package     stubbles
 * @subpackage  rdbms
 */
class stubDatabaseException extends stubChainedException
{
    // intentionally empty
}
?>