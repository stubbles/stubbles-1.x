<?php
/**
 * Criterion to check if something is one of a list of expected values.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubInCriterion.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::rdbms::criteria::stubAbstractCriterion'
);
/**
 * Criterion to check if something is one of a list of expected values.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
class stubInCriterion extends stubAbstractCriterion
{
    /**
     * constructor
     *
     * @param   string  $fieldName    the fieldname that contains the value to search for
     * @param   array   $searchValue  the values to search for
     * @param   string  $tableName    optional  the name of the table where the field is in
     * @throws  stubIllegalArgumentException
     */
    public function __construct($fieldName, $searchValue, $tableName = null)
    {
        if (is_array($searchValue) == false) {
            throw new stubIllegalArgumentException('SeachValue for IN criteria must be of type array.');
        }
        
        parent::__construct($fieldName, $searchValue, $tableName);
    }
    
    /**
     * returns the operator of the criterion
     *
     * @return  string
     */
    protected function getOperator()
    {
        return 'IN';
    }
    
    /**
     * returns the the search value
     *
     * @return  string
     */
    protected function getSearchValue()
    {
        return "('" . join("', '", $this->searchValue) . "')";
    }
}
?>