<?php
/**
 * Wrapper around the PDOStatement object.
 *
 * @package     stubbles
 * @subpackage  rdbms_pdo
 * @version     $Id: stubDatabasePDOStatement.php 2790 2010-11-25 21:48:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::rdbms::stubDatabaseStatement',
                      'net::stubbles::rdbms::stubDatabaseResult'
);
/**
 * Wrapper around the PDOStatement object.
 *
 * @package     stubbles
 * @subpackage  rdbms_pdo
 * @see         http://php.net/pdo
 */
class stubDatabasePDOStatement extends stubBaseObject implements stubDatabaseStatement, stubDatabaseResult
{
    /**
     * the wrapped pdo statement
     *
     * @var  PDOStatement
     */
    protected $pdoStatement;

    /**
     * constructor
     *
     * @param  PDOStatement  $pdoStatement  the pdo statement to wrap
     */
    public function __construct(PDOStatement $pdoStatement)
    {
        $this->pdoStatement = $pdoStatement;
    }

    /**
     * redirects calls on non-existing methods to the pdo statement object
     * 
     * @param   string  $method     name of the method to call
     * @param   array   $arguments  list of arguments for the method call
     * @return  mixed
     * @throws  stubDatabaseException
     */
    public function __call($method, $arguments)
    {
        try {
            return call_user_func_array(array($this->pdoStatement, $method), $arguments);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * bind a result column to a variable
     *
     * @param   int|string  $column     column number or name to bind the variable to
     * @param   mixed       &$variable  the variable to bind to the column
     * @param   int|string  $type       optional  type of the binded variable
     * @return  bool        true on success, false on failure
     * @throws  stubDatabaseException
     * @see     http://php.net/pdostatement-bindColumn
     * @see     net::stubbles::rdbms::stubDatabaseResult::bindColumn()
     */
    public function bindColumn($column, &$variable, $type = null)
    {
        try {
            return $this->pdoStatement->bindColumn($column, $variable, $type, null, null);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

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
     * @see     http://php.net/pdostatement-bindParam
     * @see     net::stubbles::rdbms::stubDatabaseStatement::bindParam()
     */
    public function bindParam($param, &$variable, $type = null, $length = null)
    {
        try {
            return $this->pdoStatement->bindParam($param, $variable, $type, $length, null);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

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
     * @see     http://php.net/pdostatement-bindValue
     * @see     net::stubbles::rdbms::stubDatabaseStatement::bindValue()
     */
    public function bindValue($param, $value, $type = null)
    {
        try {
            return $this->pdoStatement->bindValue($param, $value, $type);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * executes a prepared statement
     *
     * @param   array  $values  optional  specifies all necessary information for bindParam()
     *                                    the array elements must use keys corresponding to the
     *                                    number of the position or name of the parameter
     * @return  stubDatabaseResult
     * @throws  stubDatabaseException
     * @see     http://php.net/pdostatement-execute
     * @see     net::stubbles::rdbms::stubDatabaseStatement::execute()
     */
    public function execute(array $values = array())
    {
        try {
            if (true === $this->pdoStatement->execute($values)) {
                return $this;
            }
            
            throw new stubDatabaseException('Executing the prepared statement failed.');
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * fetch a result
     *
     * @param   int    $fetchMode      optional  the mode to use for fetching the data
     * @param   array  $driverOptions  optional  driver specific arguments
     * @return  mixed
     * @throws  stubDatabaseException
     * @see     http://php.net/pdostatement-fetch
     * @see     net::stubbles::rdbms::stubDatabaseResult::fetch()
     */
    public function fetch($fetchMode = null, array $driverOptions = array())
    {
        if (null === $fetchMode) {
            $fetchMode = PDO::FETCH_BOTH;
        }
        
        try {
            return $this->pdoStatement->fetch($fetchMode,
                                              ((isset($driverOptions['cursorOrientation']) == false) ? (null) : ($driverOptions['cursorOrientation'])),
                                              ((isset($driverOptions['cursorOffset']) == false) ? (null) : ($driverOptions['cursorOffset']))
                   );
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * fetch single column from the next row from a result set
     *
     * @param   int     $columnNumber  optional  the column number to fetch, default is first column
     * @return  string
     * @throws  stubDatabaseException
     * @see     http://php.net/pdostatement-fetchColumn
     * @see     net::stubbles::rdbms::stubDatabaseResult::fetchOne()
     */
    public function fetchOne($columnNumber = 0)
    {
        try {
            return $this->pdoStatement->fetchColumn($columnNumber);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * returns an array containing all of the result set rows
     *
     * @param   int    $fetchMode      optional  the mode to use for fetching the data
     * @param   array  $driverOptions  optional  driver specific arguments
     * @return  array
     * @throws  stubDatabaseException
     * @throws  stubIllegalArgumentException
     * @see     http://php.net/pdostatement-fetchAll
     * @see     net::stubbles::rdbms::stubDatabaseResult::fetchAll()
     */
    public function fetchAll($fetchMode = null, array $driverOptions = array())
    {
        try {
            if (null === $fetchMode) {
                return $this->pdoStatement->fetchAll();
            }
            
            if (PDO::FETCH_COLUMN == $fetchMode) {
                return $this->pdoStatement->fetchAll(PDO::FETCH_COLUMN,
                                                     ((isset($driverOptions['columnIndex']) === false) ? (0) : ($driverOptions['columnIndex']))
                       );
            }

            if (PDO::FETCH_CLASS == $fetchMode) {
                if (isset($driverOptions['classname']) === false) {
                    throw new stubIllegalArgumentException('Tried to use PDO::FETCH_CLASS but no classname given in driver options.');
                }
                
                return $this->pdoStatement->fetchAll(PDO::FETCH_CLASS,
                                                     $driverOptions['classname'],
                                                     ((isset($driverOptions['arguments']) === false) ? (null) : ($driverOptions['arguments']))
                       );
            }

            if (PDO::FETCH_FUNC == $fetchMode) {
                if (isset($driverOptions['function']) === false) {
                    throw new stubIllegalArgumentException('Tried to use PDO::FETCH_FUNC but no function given in driver options.');
                }

                return $this->pdoStatement->fetchAll(PDO::FETCH_FUNC,
                                                     $driverOptions['function']
                       );
            }
            
            return $this->pdoStatement->fetchAll($fetchMode);
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * moves the internal result pointer to the next result row
     *
     * @return  bool  true on success, false on failure
     * @throws  stubDatabaseException
     * @see     http://php.net/pdostatement-nextRowset
     * @see     net::stubbles::rdbms::stubDatabaseResult::next()
     */
    public function next()
    {
        try {
            return $this->pdoStatement->nextRowset();
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * returns the number of rows affected by the last SQL statement
     *
     * @return  int
     * @throws  stubDatabaseException
     * @see     http://php.net/pdostatement-rowCount
     * @see     net::stubbles::rdbms::stubDatabaseResult::count()
     */
    public function count()
    {
        try {
            return $this->pdoStatement->rowCount();
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * releases resources allocated for the specified prepared query
     * 
     * Frees up the connection to the server so that other SQL statements may
     * be issued, but leaves the statement in a state that enables it to be
     * executed again.
     *
     * @return  bool  true on success, false on failure
     * @throws  stubDatabaseException
     * @see     http://php.net/pdostatement-closeCursor
     * @see     net::stubbles::rdbms::stubDatabaseResult::free()
     */
    public function free()
    {
        try {
            return $this->pdoStatement->closeCursor();
        } catch (PDOException $pdoe) {
            throw new stubDatabaseException($pdoe->getMessage(), $pdoe);
        }
    }

    /**
     * releases resources allocated for the specified prepared query
     * 
     * Frees up the connection to the server so that other SQL statements may
     * be issued, but leaves the statement in a state that enables it to be
     * executed again.
     *
     * @return  bool  true on success, false on failure
     * @see     net::stubbles::rdbms::stubDatabaseStatement::clean()
     */
    public function clean()
    {
        $this->free();
    }
}
?>