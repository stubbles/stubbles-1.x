<?php
/**
 * wrapper around the pdo connection
 *
 * @package     stubbles
 * @subpackage  rdbms_pdo
 * @version     $Id: stubDatabasePDOConnection.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseConnection',
                      'net::stubbles::rdbms::pdo::stubDatabasePDOStatement'
);
/**
 * wrapper around the pdo connection
 *
 * @package     stubbles
 * @subpackage  rdbms_pdo
 * @see         http://php.net/pdo
 */
class stubDatabasePDOConnection extends stubBaseObject implements stubDatabaseConnection
{
    /**
     * container that contains the data required to establish the connection
     *
     * @var  stubDatabaseConnectionData
     */
    protected $connectionData;
    /**
     * instance of pdo
     * 
     * @var  PDO
     */
    protected  $pdo           = null;

    /**
     * constructor
     *
     * @param   stubDatabaseConnectionData  $connectionData  container that contains the data required to establish the connection
     * @throws  stubRuntimeException
     */
    public function __construct(stubDatabaseConnectionData $connectionData)
    {
        if (extension_loaded('pdo') == false) {
            throw new stubRuntimeException('Can not create ' . __CLASS__ . ', requires PHP-extension "pdo".');
        }
        
        $this->connectionData = $connectionData;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * establishes the connection
     * 
     * @throws  stubDatabaseException
     */
    public function connect()
    {
        if (null !== $this->pdo) {
            return;
        }
        
        try {
            $this->createPDO();
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if ($this->connectionData->hasInitialQuery() === true) {
                $this->pdo->query($this->connectionData->getInitialQuery());
            }
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * tries to create a new pdo instance
     * 
     * @throws  PDOException
     */
    // @codeCoverageIgnoreStart
    protected function createPDO()
    {
        $driverOptions = $this->connectionData->getDriverOptions();
        if (count($driverOptions) === 0) {
            $this->pdo = new PDO($this->connectionData->getDSN(),
                                 $this->connectionData->getUserName(),
                                 $this->connectionData->getPassword()
                         );
        } else {
            $this->pdo = new PDO($this->connectionData->getDSN(),
                                 $this->connectionData->getUserName(),
                                 $this->connectionData->getPassword(),
                                 $driverOptions
                         );
        }
    }
    // @codeCoverageIgnoreEnd

    /**
     * disconnects the database
     */
    public function disconnect()
    {
        $this->pdo = null;
    }

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
     * redirects calls on non-existing methods to the pdo object
     * 
     * @param   string  $method     name of the method to call
     * @param   array   $arguments  list of arguments for the method call
     * @return  mixed
     * @throws  stubDatabaseException
     */
    public function __call($method, $arguments)
    {
        if (null === $this->pdo) {
            $this->connect();
        }
        
        if (method_exists($this->pdo, $method) == false) {
            throw new stubDatabaseException('Call to undefined method ' . $this->getClassName() . '::' . $method . '().');
        }
        
        try {
            return call_user_func_array(array($this->pdo, $method), $arguments);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * start a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function beginTransaction()
    {
        return $this->__call('beginTransaction', array());
    }

    /**
     * commit a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function commit()
    {
        return $this->__call('commit', array());
    }

    /**
     * rollback a transaction
     *
     * @return  bool
     * @throws  stubDatabaseException
     */
    public function rollback()
    {
        return $this->__call('rollBack', array());
    }

    /**
     * creates a prepared statement
     * 
     * @param   string  $statement      SQL statement
     * @param   array   $driverOptions  optional  one or more key=>value pairs to set attribute values for the Statement object
     * @return  stubDatabasePDOStatement
     * @throws  stubDatabaseException
     * @see     http://php.net/pdo-prepare
     */
    public function prepare($statement, array $driverOptions = array())
    {
        if (null === $this->pdo) {
            $this->connect();
        }
        
        try {
            $statement = new stubDatabasePDOStatement($this->pdo->prepare($statement, $driverOptions));
            return $statement;
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * executes a SQL statement
     * 
     * The driver options can be:
     * <code>
     * fetchMode => one of the PDO::FETCH_* constants
     * colNo     => if fetchMode == PDO::FETCH_COLUMN this denotes the column number to fetch
     * object    => if fetchMode == PDO::FETCH_INTO this denotes the object to fetch the data into
     * classname => if fetchMode == PDO::FETCH_CLASS this denotes the class to create and fetch the data into
     * ctorargs  => (optional) if fetchMode == PDO::FETCH_CLASS this denotes the list of arguments for the constructor of the class to create and fetch the data into
     * </code>
     * 
     * @param   string  $sql            the sql query to use
     * @param   array   $driverOptions  optional  how to fetch the data
     * @return  stubDatabasePDOStatement
     * @throws  stubDatabaseException
     * @see     http://php.net/pdo-query
     * @see     http://php.net/pdostatement-setfetchmode for the details on the fetch mode options
     */
    public function query($sql, array $driverOptions = array())
    {
        if (null === $this->pdo) {
            $this->connect();
        }
        
        try {
            if (isset($driverOptions['fetchMode']) == true) {
                switch ($driverOptions['fetchMode']) {
                    case PDO::FETCH_COLUMN:
                        if (isset($driverOptions['colNo']) == false) {
                            throw new stubDatabaseException('Fetch mode COLUMN requires driver option �colNo�.');
                        }
                        
                        $pdoStatement = $this->pdo->query($sql, $driverOptions['fetchMode'], $driverOptions['colNo']);
                        break;
                    
                    case PDO::FETCH_INTO:
                        if (isset($driverOptions['object']) == false) {
                            throw new stubDatabaseException('Fetch mode INTO requires driver option �object�.');
                        }
                        
                        $pdoStatement = $this->pdo->query($sql, $driverOptions['fetchMode'], $driverOptions['object']);
                        break;
                    
                    case PDO::FETCH_CLASS:
                        if (isset($driverOptions['classname']) == false) {
                            throw new stubDatabaseException('Fetch mode CLASS requires driver option �classname�.');
                        }
                        
                        if (isset($driverOptions['ctorargs']) == false) {
                            $driverOptions['ctorargs'] = array();
                        }
                        
                        $pdoStatement = $this->pdo->query($sql, $driverOptions['fetchMode'], $driverOptions['classname'], $driverOptions['ctorargs']);
                        break;
                    
                    default:
                        $pdoStatement = $this->pdo->query($sql, $driverOptions['fetchMode']);
                }
            } else {
                $pdoStatement = $this->pdo->query($sql);
            }
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
        
        $statement = new stubDatabasePDOStatement($pdoStatement);
        return $statement;
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
        if (null === $this->pdo) {
            $this->connect();
        }
        
        try {
            return $this->pdo->exec($statement);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * returns the last insert id
     *
     * @param   string  $name  optional  name of the sequence object from which the ID should be returned.
     * @return  int
     * @throws  stubDatabaseException
     */
    public function getLastInsertId($name = null)
    {
        if (null === $this->pdo) {
            throw new stubDatabaseException('Not connected: can not retrieve last insert id.');
        }
        
        try {
            return $this->pdo->lastInsertId($name);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * returns the database name (e.g. MySQL or PostgreSQL)
     *
     * @return  string
     */
    public function getDatabase()
    {
        $dsnParts = explode(':', $this->connectionData->getDSN());
        return $dsnParts[0];
    }
}
?>