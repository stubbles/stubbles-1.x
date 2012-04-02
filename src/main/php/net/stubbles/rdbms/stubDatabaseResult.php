<?php
/**
 * Interface for database query results.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @version     $Id: stubDatabaseResult.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseException');
/**
 * Interface for database query results.
 *
 * @package     stubbles
 * @subpackage  rdbms
 */
interface stubDatabaseResult extends stubObject
{
    /**
     * bind a result column to a variable
     *
     * @param   int|string  $column     column number or name to bind the variable to
     * @param   mixed       &$variable  the variable to bind to the column
     * @param   int|string  $type       optional  type of the binded variable
     * @return  bool        true on success, false on failure
     * @throws  stubDatabaseException
     */
    public function bindColumn($column, &$variable, $type = null);
    
    /**
     * fetch a result
     *
     * @param   int    $fetchMode      optional  the mode to use for fetching the data
     * @param   array  $driverOptions  optional  driver specific arguments
     * @return  mixed
     * @throws  stubDatabaseException
     */
    public function fetch($fetchMode = null, array $driverOptions = array());
    
    /**
     * fetch single column from the next row from a result set
     *
     * @param   int     $columnNumber  optional  the column number to fetch, default is first column
     * @return  string
     * @throws  stubDatabaseException
     */
    public function fetchOne($columnNumber = 0);
    
    /**
     * returns an array containing all of the result set rows
     *
     * @param   int    $fetchMode      optional  the mode to use for fetching the data
     * @param   array  $driverOptions  optional  driver specific arguments
     * @return  array
     * @throws  stubDatabaseException
     */
    public function fetchAll($fetchMode = null, array $driverOptions = array());
    
    /**
     * moves the internal result pointer to the next result row
     *
     * @return  bool  true on success, false on failure
     * @throws  stubDatabaseException
     */
    public function next();
    
    /**
     * returns the number of rows affected by the last SQL statement
     *
     * @return  int
     * @throws  stubDatabaseException
     */
    public function count();
    
    /**
     * releases resources allocated of the result set
     *
     * @return  bool  true on success, false on failure
     * @throws  stubDatabaseException
     */
    public function free();
}
?>