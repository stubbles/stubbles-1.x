<?php
/**
 * Interface for database statements.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @version     $Id: stubDatabaseStatement.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseException');
/**
 * Interface for database statements.
 *
 * @package     stubbles
 * @subpackage  rdbms
 */
interface stubDatabaseStatement extends stubObject
{
    /**
     * bind a parameter of a prepared query to the specified variable
     * 
     * The binding will be via reference, so it is evaluated at the time when
     * the prepared statement is executed meaning that in opposite to
     * bindValue() the value of the variable at the time of execution will be
     * used, not the value at the time when this method is called.
     *
     * @param   int|string  $param      the order number of the parameter or its name
     * @param   mixed       &$variable  the variable to bind to the parameter
     * @param   int|string  $type       optional  type of the parameter
     * @param   int         $length     optional  length of the data type
     * @return  bool        true on success, false on failure
     * @throws  stubDatabaseException
     */
    public function bindParam($param, &$variable, $type = null, $length = null);
    
    /**
     * bind a value to the parameter of a prepared query
     * 
     * In opposite to bindParam() this will use the value as it is at the time
     * when this method is called.
     * 
     * @param   int|string  $param  the order number of the parameter or its name
     * @param   mixed       $value  the value to bind
     * @param   int|string  $type   optional  type of the parameter
     * @return  bool        true on success, false on failure
     * @throws  stubDatabaseException
     */
    public function bindValue($param, $value, $type = null);
    
    /**
     * executes a prepared statement
     *
     * @param   array  $values  optional  specifies all necessary information for bindParam()
     *                                    the array elements must use keys corresponding to the
     *                                    number of the position or name of the parameter
     * @return  stubDatabaseResult
     * @throws  stubDatabaseException
     */
    public function execute(array $values = array());
    
    /**
     * releases resources allocated for the specified prepared query
     * 
     * Frees up the connection to the server so that other SQL statements may
     * be issued, but leaves the statement in a state that enables it to be
     * executed again.
     *
     * @return  bool  true on success, false on failure
     * @throws  stubDatabaseException
     */
    public function clean();
}
?>