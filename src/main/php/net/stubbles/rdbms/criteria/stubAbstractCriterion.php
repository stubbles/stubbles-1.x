<?php
/**
 * Base criterion class.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubAbstractCriterion.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubCriterion');
/**
 * Base criterion class.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
abstract class stubAbstractCriterion extends stubBaseObject implements stubCriterion
{
    /**
     * the name of the table where the field is in
     *
     * @var  string
     */
    protected $tableName;
    /**
     * the fieldname that contains the value to search for
     *
     * @var  string
     */
    protected $fieldName;
    /**
     * the value to search for
     *
     * @var  scalar
     */
    protected $searchValue;
    
    /**
     * constructor
     *
     * @param  string  $fieldName    the fieldname that contains the value to search for
     * @param  string  $searchValue  the value to search for
     * @param  string  $tableName    optional  the name of the table where the field is in
     */
    public function __construct($fieldName, $searchValue, $tableName = null)
    {
        $this->tableName   = $tableName;
        $this->fieldName   = $fieldName;
        $this->searchValue = $searchValue;
    }
    
    /**
     * returns the criterion as sql
     * 
     * @return  string
     */
    public function toSQL()
    {
        $sql = '`' . $this->fieldName . '` ' . $this->getOperator() . ' ' . $this->getSearchValue();
        if (null != $this->tableName) {
            $sql = '`' . $this->tableName . '`.' . $sql;
        }
        
        return $sql;
    }
    
    /**
     * returns the operator of the criteria
     *
     * @return  string
     */
    protected abstract function getOperator();
    
    /**
     * returns the the search value
     *
     * @return  string
     */
    protected function getSearchValue()
    {
        return "'" . $this->searchValue . "'";
    }
}
?>