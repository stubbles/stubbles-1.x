<?php
/**
 * Container for database connection data.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @version     $Id: stubDatabaseConnectionData.php 2471 2010-01-18 16:50:55Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubConfigurationException');
/**
 * Container for database connection data.
 *
 * @package     stubbles
 * @subpackage  rdbms
 */
class stubDatabaseConnectionData extends stubSerializableObject
{
    /**
     * id of the default connection
     */
    const DEFAULT_ID         = 'default';
    /**
     * id to use for the connection
     *
     * @var  string
     */
    protected $id            = self::DEFAULT_ID;
    /**
     * the full qualified name of the class to use for the connection
     *
     * @var  string
     */
    protected $fqClassName   = 'net::stubbles::rdbms::pdo::stubDatabasePDOConnection';
    /**
     * Data Source Name, or DSN, contains the information required to connect to the database
     *
     * @var  string
     */
    protected $dsn           = '';
    /**
     * user name
     *
     * @var  string
     */
    protected $userName      = '';
    /**
     * password
     *
     * @var  string
     */
    protected $password      = '';
    /**
     * a key=>value array of driver-specific connection options
     *
     * @var  array
     */
    protected $driverOptions = array();
    /**
     * initial query to be executed after commit
     *
     * @var  string
     */
    protected $initialQuery;

    /**
     * create connection data instance from an array
     *
     * Please note that this method does not support driver options. Driver
     * options must be set separately.
     *
     * @param   array<string,string>        $properties
     * @param   string                      $id          optional
     * @return  stubDatabaseConnectionData
     * @since   1.1.0
     */
    public static function fromArray(array $properties, $id = null)
    {
        $self = new self();
        if (null != $id) {
            $self->setId($id);
        }
        
        if (isset($properties['dsn']) === false) {
            throw new stubConfigurationException('Missing dsn property.');
        }

        $self->setDSN($properties['dsn']);
        if (isset($properties['username']) === true) {
            $self->setUserName($properties['username']);
        }

        if (isset($properties['password']) === true) {
            $self->setPassword($properties['password']);
        }

        if (isset($properties['initialQuery']) === true) {
            $self->setInitialQuery($properties['initialQuery']);
        }

        if (isset($properties['connectionClassName']) === true) {
            $self->setConnectionClassName($properties['connectionClassName']);
        }

        return $self;
    }

    /**
     * set the id to use for the connection
     * 
     * Warning: two instances will be the same if they have the same id,
     * regardless whether the concrete connection data is differant or not.
     * You should never use the same id for differant connection datasets.
     *
     * @param  string  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * return the id to use for the connection
     *
     * @return  string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * checks whether a value is equal to the class
     * 
     * Warning: two instances will be the same if they have the same id,
     * regardless whether the concrete connection data is differant or not.
     * You should never use the same id for differant connection datasets.
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($this->getId() == $compare->getId());
        }
        
        return false;
    }

    /**
     * sets the full qualified name of the class to use for the connection
     *
     * @param  string  $fqClassName
     */
    public function setConnectionClassName($fqClassName)
    {
        $this->fqClassName = $fqClassName;
    }

    /**
     * returns the full qualified name of the class to use for the connection
     *
     * @return  string
     */
    public function getConnectionClassName()
    {
        return $this->fqClassName;
    }

    /**
     * sets the Data Source Name
     *
     * @param  string  $dsn
     */
    public function setDSN($dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * returns the Data Source Name
     *
     * @return  string
     */
    public function getDSN()
    {
        return $this->dsn;
    }

    /**
     * sets the user name
     *
     * @param  string  $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * returns the user name
     *
     * @return  string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * sets the password
     *
     * @param  string  $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * returns the user password
     *
     * @return  string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * sets a key=>value array of driver-specific connection options
     *
     * @param  array  $driverOptions
     */
    public function setDriverOptions(array $driverOptions)
    {
        $this->driverOptions = $driverOptions;
    }

    /**
     * returns a key=>value array of driver-specific connection options
     *
     * @return  array
     */
    public function getDriverOptions()
    {
        return $this->driverOptions;
    }

    /**
     * sets initial query to be send after establishing the connection
     *
     * @param  string  $initialQuery
     */
    public function setInitialQuery($initialQuery)
    {
        $this->initialQuery = $initialQuery;
    }

    /**
     * checks if an initial query should be send
     *
     * @return  string
     */
    public function hasInitialQuery()
    {
        return (null != $this->initialQuery);
    }

    /**
     * returns initial query to be send after establishing the connection
     *
     * @return  string
     */
    public function getInitialQuery()
    {
        return $this->initialQuery;
    }
}
?>