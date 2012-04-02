<?php
/**
 * IoC provider for database connections.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc
 * @version     $Id: stubDatabaseConnectionProvider.php 2501 2010-02-04 15:52:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::rdbms::stubDatabaseConnectionData',
                      'net::stubbles::rdbms::stubDatabaseException',
                      'net::stubbles::rdbms::stubDatabaseInitializer'
);
/**
 * IoC provider for database connections.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc
 */
class stubDatabaseConnectionProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * database connection data initializer
     *
     * @var  stubDatabaseInitializer
     */
    protected $dbInitializer;
    /**
     * set of database connections
     *
     * @var  array<string,stubDatabaseConnection>
     */
    protected $connections   = array();
    /**
     * switch whether to fallback to default connection if no named connection exists
     *
     * @var  bool
     */
    protected $fallback      = true;

    /**
     * constructor
     *
     * @param  stubDatabaseInitializer  $dbInitializer  database connection data initializer
     * @Inject
     */
    public function __construct(stubDatabaseInitializer $dbInitializer)
    {
        $this->dbInitializer = $dbInitializer;
    }

    /**
     *
     * @param   bool                            $fallback  whether to fallback to default connection if no named connection exists
     * @return  stubDatabaseConnectionProvider
     * @Inject(optional=true)
     * @Named('net.stubbles.rdbms.fallback')
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;
        return $this;
    }

    /**
     * returns the connection to be injected
     *
     * If a name is provided and a connection with this name exists this
     * connection will be returned. If fallback is enabled and the named
     * connection does not exist the default connection will be returned, if
     * fallback is disabled a stubDatabaseException will be thrown.
     *
     * If no name is provided the default connection will be returned.
     *
     * @param   string                  $name  optional
     * @return  stubDatabaseConnection
     * @throws  stubDatabaseException
     */
    public function get($name = null)
    {
        if (null == $name) {
            return $this->get(stubDatabaseConnectionData::DEFAULT_ID);
        }
        
        if (isset($this->connections[$name]) === true) {
            return $this->connections[$name];
        }

        if ($this->dbInitializer->hasConnectionData($name) === false) {
            if (stubDatabaseConnectionData::DEFAULT_ID !== $name && true === $this->fallback) {
                return $this->get(stubDatabaseConnectionData::DEFAULT_ID);
            }
            
            throw new stubDatabaseException('No connection and no dsn known for connection associated with id ' . $name);
        }
        
        $connectionData      = $this->dbInitializer->getConnectionData($name);
        $connectionClassName = $connectionData->getConnectionClassName();
        $nqClassName         = stubClassLoader::getNonQualifiedClassName($connectionClassName);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($connectionClassName);
        }
         
        $connection = new $nqClassName($connectionData);
        if (($connection instanceof stubDatabaseConnection) === false) {
            throw new stubDatabaseException($connectionClassName . ' is not an instance of net::stubbles::rdbms::stubDatabaseConnection.');
        }
        
        $this->connections[$name] = $connection;
        return $connection;
    }
}
?>