<?php
/**
 * QueryBuilder that returns its arguments.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @version     $Id: TeststubDatabaseQueryBuilder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseQueryBuilder');
/**
 * QueryBuilder that returns its arguments.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @group       rdbms
 * @group       rdbms_querybuilder
 */
class TeststubDatabaseQueryBuilder extends stubBaseObject implements stubDatabaseQueryBuilder
{
    /**
     * the select query to be returned on createSelect()
     *
     * @var  string
     */
    protected $selectQuery      = '';
    /**
     * the queries to be returned on createInsert()
     *
     * @var  array
     */
    protected $insertQueries    = array();
    /**
     * the queries to be returned on createUpdate()
     *
     * @var  array
     */
    protected $updateQueries    = array();
    /**
     * the query to be returned on createDelete()
     *
     * @var  string
     */
    protected $deleteQuery      = '';
    /**
     * the create table query to be returned on createTable()
     *
     * @var  string
     */
    protected $createTableQuery = '';
    /**
     * select data container
     *
     * @var  stubDatabaseSelect
     */
    protected $select           = null;
    /**
     * table rows to insert from the createInsert() method
     *
     * @var  array
     */
    protected $insertTableRows  = null;
    /**
     * table rows to update from the createUpdate() method
     *
     * @var  array
     */
    protected $updateTableRows  = null;
    /**
     * the table from the createDelete() method
     *
     * @var  string
     */
    protected $deleteTable      = null;
    /**
     * the criterion from the createDelete() method
     *
     * @var  stubCriterion
     */
    protected $deleteCriterion  = null;
    /**
     * table description from the createTable() method
     *
     * @var  stubDatabaseTableDescription
     */
    protected $tableDescription = null;
    /**
     * call count for methods
     *
     * @var  array<string,int>
     */
    protected $callCount        = array();

    /**
     * sets the select query to return on createSelect()
     *
     * @param  string  $select
     */
    public function setSelectQuery($selectQuery)
    {
        $this->selectQuery = $selectQuery;
    }

    /**
     * creates a select query
     *
     * @param   stubDatabaseSelect  $select
     * @return  string
     */
    public function createSelect(stubDatabaseSelect $select)
    {
        $this->raiseCallCount(__FUNCTION__);
        $this->select = $select;
        return $this->selectQuery;
    }

    /**
     * returns the select data container from the createSelect() method
     *
     * @return  stubDatabaseSelect
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * set the insert queries to be returned on createInsert()
     *
     * @param  array  $insertQueries
     */
    public function setInsertQueries(array $insertQueries)
    {
        $this->insertQueries = $insertQueries;
    }

    /**
     * creates insert queries from a serialized value
     *
     * @param   array<string,stubDatabaseTableRow>  $tableRows
     * @return  array<string,string>
     */
    public function createInsert(array $tableRows)
    {
        $this->raiseCallCount(__FUNCTION__);
        $this->insertTableRows = $tableRows;
        return $this->insertQueries;
    }

    /**
     * returns the table rows to insert from the createInsert() method
     *
     * @return  array<string,stubDatabaseTableRow>
     */
    public function getInsertTableRows()
    {
        return $this->insertTableRows;
    }

    /**
     * set the update queries to be returned on createUpdate()
     *
     * @param  array  $updateQueries
     */
    public function setUpdateQueries(array $updateQueries)
    {
        $this->updateQueries = $updateQueries;
    }

    /**
     * creates update queries from a serialized value
     *
     * @param   array<string,stubDatabaseTableRow>  $values
     * @return  array<string,string>
     */
    public function createUpdate(array $tableRows)
    {
        $this->raiseCallCount(__FUNCTION__);
        $this->updateTableRows = $tableRows;
        return $this->updateQueries;
    }

    /**
     * returns the table rows to update from the createUpdate() method
     *
     * @return  array<string,stubDatabaseTableRow>
     */
    public function getUpdateTableRows()
    {
        return $this->updateTableRows;
    }

    /**
     * set the delete query to be returned on createDelete()
     *
     * @param  string  $deleteQuery
     */
    public function setDeleteQuery($deleteQuery)
    {
        $this->deleteQuery = $deleteQuery;
    }

    /**
     * creates a delete query
     *
     * @param   string         $table      the table to delete from
     * @param   stubCriterion  $criterion  the criterion to use for deletion
     * @return  string
     */
    public function createDelete($table, stubCriterion $criterion)
    {
        $this->deleteTable     = $table;
        $this->deleteCriterion = $criterion;
        return $this->deleteQuery;
    }

    /**
     * returns the delete table argument
     *
     * @return  string
     */
    public function getDeleteTable()
    {
        return $this->deleteTable;
    }

    /**
     * returns the delete criterion argument
     *
     * @return  stubCriterion
     */
    public function getDeleteCriterion()
    {
        return $this->deleteCriterion;
    }

    /**
     * sets the create table query to return on createTable()
     *
     * @param  string  $createTableQuery
     */
    public function setCreateTableQuery($createTableQuery)
    {
        $this->createTableQuery = $createTableQuery;
    }

    /**
     * creates the query to create a table for the given class
     *
     * @param   stubDatabaseTableDescription       $tableDescription
     * @throws  stubDatabaseQueryBuilderException
     */
    public function createTable(stubDatabaseTableDescription $tableDescription)
    {
        $this->raiseCallCount(__FUNCTION__);
        $this->tableDescription = $tableDescription;
        return $this->createTableQuery;
    }

    /**
     * returns the table description from the createTable() method
     *
     * @return  stubDatabaseTableDescription
     */
    public function getTableDescription()
    {
        return $this->tableDescription;
    }

    /**
     * raises the callcount of a method
     *
     * @param  string  $methodName  name of the method to raise the callcount for
     */
    protected function raiseCallCount($methodName)
    {
        if (isset($this->callCount[$methodName]) == false) {
            $this->callCount[$methodName] = 0;
        }
        
        $this->callCount[$methodName]++;
    }

    /**
     * returns the amount of calls to the given method
     *
     * @param   string  $methodName  name of the method to get the callcount from
     * @return  int
     */
    public function getCallCount($methodName)
    {
        if (isset($this->callCount[$methodName]) == false) {
            return 0;
        }
        
        return $this->callCount[$methodName];
    }
}
?>