<?php
/**
 * Exception to be thrown if a problem in the eraser occurs.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_eraser
 * @version     $Id: stubDatabaseEraserException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception to be thrown if a problem in the eraser occurs.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_eraser
 */
class stubDatabaseEraserException extends stubChainedException
{
    // intentionally empty
}
?>