<?php
/**
 * Class that represents a row within a table.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @version     $Id: stubDatabaseTableRow.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubCriterion',
                      'net::stubbles::rdbms::criteria::stubAndCriterion'
);
/**
 * Class that represents a row within a table.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 */
class stubDatabaseTableRow extends stubBaseObject
{
    /**
     * name of the table the row is from
     *
     * @var  string
     */
    protected $tableName = '';
    /**
     * the list of values of the row
     *
     * @var  array<string,scalar>
     */
    protected $columns   = array();
    /**
     * the criterion that identifies this row uniquely
     *
     * @var  stubAndCriterion
     */
    protected $criterion;

    /**
     * constructor
     * 
     * @param  string  $tableName  name of the table where this row is from
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        $this->criterion = new stubAndCriterion();
    }

    /**
     * returns the name of the table the row is from
     *
     * @return  string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * sets the value of a column
     *
     * @param  string       $name   name of the column to set
     * @param  scalar|null  $value  value the column should have
     */
    public function setColumn($name, $value)
    {
        $this->columns[$name] = $value;
    }

    /**
     * returns a list of all column names
     *
     * @return  array<string>
     */
    public function getColumnNames()
    {
        return array_keys($this->columns);
    }
    
    /**
     * returns a hash map of columns
     *
     * @return  array<string,scalar>
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * adds a criterion to the and criterion
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
}
?>