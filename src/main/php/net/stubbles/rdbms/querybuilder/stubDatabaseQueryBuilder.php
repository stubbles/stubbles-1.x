<?php
/**
 * Interface for database specific query builders.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @version     $Id: stubDatabaseQueryBuilder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::rdbms::criteria::stubCriterion',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseQueryBuilderException',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseSelect',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableDescription',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableRow'
);
/**
 * Interface for database specific query builders.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 */
interface stubDatabaseQueryBuilder extends stubObject
{
    /**
     * creates a select query
     *
     * @param   stubDatabaseSelect  $select
     * @return  string
     */
    public function createSelect(stubDatabaseSelect $select);

    /**
     * creates insert queries from a serialized value
     *
     * @param   array<string,stubDatabaseTableRow>  $tableRows
     * @return  array<string,string>
     * @throws  stubIllegalArgumentException
     */
    public function createInsert(array $tableRows);

    /**
     * creates update queries from a serialized value
     *
     * @param   array<string,stubDatabaseTableRow>  $tableRows
     * @return  array<string,string>
     * @throws  stubIllegalArgumentException
     */
    public function createUpdate(array $tableRows);

    /**
     * creates a delete query
     *
     * @param   string         $table      the table to delete from
     * @param   stubCriterion  $criterion  the criterion to use for deletion
     * @return  string
     */
    public function createDelete($table, stubCriterion $criterion);

    /**
     * creates the query to create a table for the given class
     *
     * @param   stubDatabaseTableDescription       $tableDescription
     * @throws  stubDatabaseQueryBuilderException
     */
    public function createTable(stubDatabaseTableDescription $tableDescription);
}
?>