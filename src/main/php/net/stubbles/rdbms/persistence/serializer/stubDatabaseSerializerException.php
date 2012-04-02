<?php
/**
 * Exception to be thrown in case the serializer locates a problem.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 * @version     $Id: stubDatabaseSerializerException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseException');
/**
 * Exception to be thrown in case the serializer locates a problem.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 */
class stubDatabaseSerializerException extends stubDatabaseException
{
    // intentionally empty
}
?>