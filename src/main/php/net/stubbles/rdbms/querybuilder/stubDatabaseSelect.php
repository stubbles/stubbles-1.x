<?php
/**
 * Container for the data of a select query.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @version     $Id: stubDatabaseSelect.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubAndCriterion',
                      'net::stubbles::rdbms::criteria::stubCriterion',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableDescription',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableJoin'
);
/**
 * Container for the data of a select query.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 */
class stubDatabaseSelect extends stubBaseObject
{
    /**
     * description of the main table
     * 
     * @var  stubDatabaseTableDescription
     */
    protected $table;
    /**
     * list of tables
     *
     * @var  array<string,stubDatabaseTableJoin>
     */
    protected $joins        = array();
    /**
     * the criterion for the select
     *
     * @var  stubAndCriterion
     */
    protected $criterion;
    /**
     * order by clause
     *
     * @var  string
     */
    protected $orderBy;
    /**
     * offset for limit clause
     *
     * @var  int
     */
    protected $offset;
    /**
     * amount for limit clause
     *
     * @var  int
     */
    protected $amount;

    /**
     * constructor
     * 
     * @param  stubDatabaseTableDescription  $tableDescription
     */
    public function __construct(stubDatabaseTableDescription $tableDescription)
    {
        $this->table     = $tableDescription;
        $this->criterion = new stubAndCriterion();
    }

    /**
     * returns the name of the base table
     *
     * @return  string
     */
    public function getBaseTableName()
    {
        return $this->table->getName();
    }

    /**
     * adds a table
     *
     * @param  stubDatabaseTableJoin  $tableJoin
     */
    public function addJoin(stubDatabaseTableJoin $tableJoin)
    {
        $this->joins[$tableJoin->getName()] = $tableJoin;
    }

    /**
     * check whether there are tables to join
     *
     * @return  bool
     */
    public function hasJoins()
    {
        return (count($this->joins) > 0);
    }

    /**
     * returns the list of tables
     *
     * @return  array<string,stubDatabaseTableJoin>
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * adds a criterion
     *
     * @param  stubCriterion  $criterion
     */
    public function addCriterion(stubCriterion $criterion)
    {
        $this->criterion->addCriterion($criterion);
    }

    /**
     * checks whether there is any criterion
     *
     * @return  bool
     */
    public function hasCriterion()
    {
        return $this->criterion->hasCriterion();
    }

    /**
     * returns the criterion
     *
     * @return  stubAndCriterion
     */
    public function getCriterion()
    {
        return $this->criterion;
    }

    /**
     * sets the order by clause
     *
     * @param   string              $orderBy
     * @return  stubDatabaseSelect
     */
    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * checks whether an order by clause is set
     *
     * @return  bool
     */
    public function isOrdered()
    {
        return (null !== $this->orderBy);
    }

    /**
     * returns the order by clause
     *
     * @return  string
     */
    public function getOrderedBy()
    {
        return  $this->orderBy;
    }

    /**
     * limits query to start at given offset and contain only given amount of results
     *
     * @param   int                 $offset
     * @param   int                 $amount
     * @return  stubDatabaseSelect
     */
    public function limitBy($offset, $amount)
    {
        $this->offset = ((null === $offset) ? (null) : ((int) $offset));
        $this->amount = ((null === $amount) ? (null) : ((int) $amount));
        return $this;
    }

    /**
     * checks if select has a limit
     *
     * @return  bool
     */
    public function hasLimit()
    {
        return (null !== $this->offset && null !== $this->amount);
    }

    /**
     * returns offset for limit clause
     *
     * @return  int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * returns amount for limit clause
     *
     * @return  int
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
?>