<?php
/**
 * Criterion to check for values equal to or greater than the search value.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubGreaterEqualCriterion.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::rdbms::criteria::stubAbstractCriterion'
);
/**
 * Criterion to check for values equal to or greater than the search value.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
class stubGreaterEqualCriterion extends stubAbstractCriterion
{
    /**
     * constructor
     *
     * @param   string  $fieldName    the fieldname that contains the value to search for
     * @param   string  $searchValue  the value to search for
     * @param   string  $tableName    optional  the name of the table where the field is in
     * @throws  stubIllegalArgumentException
     */
    public function __construct($fieldName, $searchValue, $tableName = null)
    {
        if (null === $searchValue) {
            throw new stubIllegalArgumentException('SeachValue for GREATER EQUAL criteria can not be NULL.');
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
        return '>=';
    }
}
?>