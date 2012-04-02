<?php
/**
 * Dummy implementation of a database connection.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @version     $Id: stubDatabaseDummyConnection.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseConnection');
/**
 * Dummy implementation of a database connection.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 */
class stubDatabaseDummyConnection extends stubBaseObject implements stubDatabaseConnection
{
    /**
     * connection data
     *
     * @var  stubDatabaseConnectionData
     */
    protected $connectionData;
    /**
     * constructor
     *
     * @param  stubDatabaseConnectionData  $connectionData  container that contains the data required to establish the connection
     */
    public function __construct(stubDatabaseConnectionData $connectionData)
    {
        $this->connectionData = $connectionData;
    }

    /**
     * establishes the connection
     *
     * @throws  stubDatabaseException
     */
    public function connect() { }

    /**
     * disconnects the database
     */
    public function disconnect() { }

    /**
     * returns the connection data used for the connection
     *
     * @return  stubDatabaseConnectionData
     */
    public function getConnectionData()
    {
        return $this->connectionData;
    }

    /**
     * start a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function beginTransaction()
    {
        return true;
    }

    /**
     * commit a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function commit()
    {
        return true;
    }

    /**
     * rollback a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function rollback()
    {
        return true;
    }

    /**
     * creates a prepared statement
     * 
     * @param   string  $statement      SQL statement
     * @param   array   $driverOptions  optional  one or more key=>value pairs to set attribute values for the Statement object
     * @return  stubDatabaseStatement
     * @throws  stubDatabaseException
     */
    public function prepare($statement, array $driverOptions = array())
    {
        return null;
    }

    /**
     * executes a SQL statement
     * 
     * @param   string  $sql            the sql query to use
     * @param   array   $driverOptions  optional  one or more driver specific options for the call to query()
     * @return  stubDatabaseResult
     * @throws  stubDatabaseException
     */
    public function query($sql, array $driverOptions = array())
    {
        return null;
    }

    /**
     * execute an SQL statement and return the number of affected rows
     *
     * @param   string  $statement      the sql statement to execute
     * @param   array   $driverOptions  optional  one or more driver specific options for the call to query()
     * @return  int     number of effected rows
     * @throws  stubDatabaseException
     */
    public function exec($statement, array $driverOptions = array())
    {
        return 0;
    }

    /**
     * returns the last insert id
     *
     * @param   string  $name  optional  identifier to where to retrieve the last insert id from
     * @return  int
     * @throws  stubDatabaseException
     */
    public function getLastInsertId($name = null)
    {
        return 0;
    }

    /**
     * returns the database name (e.g. MySQL or PostgreSQL)
     *
     * @return  string
     */
    public function getDatabase()
    {
        return 'Dummy';
    }
}
?>