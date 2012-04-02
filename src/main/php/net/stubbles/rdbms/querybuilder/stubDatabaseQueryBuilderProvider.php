<?php
/**
 * Provider for query builders.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @version     $Id: stubDatabaseQueryBuilderProvider.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::ioc::stubInjector',
                      'net::stubbles::rdbms::stubDatabaseConnection',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseQueryBuilderException'
);
/**
 * Provider for query builders.
 *
 * This provider can be used in two ways: either directly get an instance of it
 * and use it's create() method to retrieve the correct query builder, or
 * indirect by requesting a query builder and using the @Named annotation to
 * specify which one.
 *
 * Direct usage:
 * <code>
 * $queryBuilder = $qbProvider->create($dbConnection);
 * </code>
 *
 * Indirect usage:
 * <code>
 * /**
 *  * sets the query builder
 *  *
 *  * @param  stubDatabaseQueryBuilder  $qb
 *  * @Inject
 *  * @Named('mysql')
 *  *
 * public function setQueryBuilder(stubDatabaseQueryBuilder $qb)
 * {
 *     $this->qb = $qb;
 * }
 * </code>
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @since       1.7.0
 * @Singleton
 */
class stubDatabaseQueryBuilderProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * list of available query builders
     *
     * @var  array<string,string>
     */
    protected $availableQueryBuilders = array('mysql' => 'net::stubbles::rdbms::querybuilder::stubDatabaseMySQLQueryBuilder');

    /**
     * constructor
     *
     * @param  stubInjector  $injector
     * @Inject
     */
    public function __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * sets available query builder classes
     *
     * @param   array<string,string>              $availableQueryBuilders
     * @return  stubDatabaseQueryBuilderProvider
     * @Inject(optional=true)
     * @Named('net.stubbles.rdbms.querybuilders')
     */
    public function setAvailableQueryBuilders(array $availableQueryBuilders)
    {
        $this->availableQueryBuilders = $availableQueryBuilders;
        return $this;
    }

    /**
     * creates a query builder for the given connection
     *
     * @param   stubDatabaseConnection    $connection
     * @return  stubDatabaseQueryBuilder
     */
    public function create(stubDatabaseConnection $connection)
    {
        return $this->get(strtolower($connection->getDatabase()));
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     * @throws  stubDatabaseQueryBuilderException
     */
    public function get($name = null)
    {
        if (isset($this->availableQueryBuilders[$name]) === false) {
            throw new stubDatabaseQueryBuilderException('Could not find QueryBuilder for database ' . $name);
        }

        return $this->injector->getInstance($this->availableQueryBuilders[$name]);
    }
}
?>