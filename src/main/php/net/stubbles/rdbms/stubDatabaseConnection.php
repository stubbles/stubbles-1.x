<?php
/**
 * Interface for database connections.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @version     $Id: stubDatabaseConnection.php 2501 2010-02-04 15:52:40Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseConnectionData',
                      'net::stubbles::rdbms::stubDatabaseException',
                      'net::stubbles::rdbms::stubDatabaseResult',
                      'net::stubbles::rdbms::stubDatabaseStatement'
);
/**
 * Interface for database connections.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @ProvidedBy(net::stubbles::rdbms::ioc::stubDatabaseConnectionProvider.class)
 */
interface stubDatabaseConnection extends stubObject
{
    /**
     * constructor
     *
     * @param  stubDatabaseConnectionData  $connectionData  container that contains the data required to establish the connection
     */
    #public function __construct(stubDatabaseConnectionData $connectionData);

    /**
     * establishes the connection
     *
     * @throws  stubDatabaseException
     */
    public function connect();

    /**
     * disconnects the database
     */
    public function disconnect();

    /**
     * returns the connection data used for the connection
     *
     * @return  stubDatabaseConnectionData
     */
    public function getConnectionData();

    /**
     * start a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function beginTransaction();

    /**
     * commit a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function commit();

    /**
     * rollback a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function rollback();

    /**
     * creates a prepared statement
     * 
     * @param   string  $statement      SQL statement
     * @param   array   $driverOptions  optional  one or more key=>value pairs to set attribute values for the Statement object
     * @return  stubDatabaseStatement
     * @throws  stubDatabaseException
     */
    public function prepare($statement, array $driverOptions = array());

    /**
     * executes a SQL statement
     * 
     * @param   string  $sql            the sql query to use
     * @param   array   $driverOptions  optional  one or more driver specific options for the call to query()
     * @return  stubDatabaseResult
     * @throws  stubDatabaseException
     */
    public function query($sql, array $driverOptions = array());

    /**
     * execute an SQL statement and return the number of affected rows
     *
     * @param   string  $statement      the sql statement to execute
     * @param   array   $driverOptions  optional  one or more driver specific options for the call to query()
     * @return  int     number of effected rows
     * @throws  stubDatabaseException
     */
    public function exec($statement, array $driverOptions = array());

    /**
     * returns the last insert id
     *
     * @param   string  $name  optional  identifier to where to retrieve the last insert id from
     * @return  int
     * @throws  stubDatabaseException
     */
    public function getLastInsertId($name = null);

    /**
     * returns the database name (e.g. MySQL or PostgreSQL)
     *
     * @return  string
     */
    public function getDatabase();
}
?>