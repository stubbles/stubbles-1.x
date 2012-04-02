<?php
/**
 * Criterion to check if something is like an expected value.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubLikeCriterion.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::rdbms::criteria::stubAbstractCriterion'
);
/**
 * Criterion to check if something is like an expected value.
 * 
 * The searchValue has to contain %, it can not be appended or prepended by
 * the class.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
class stubLikeCriterion extends stubAbstractCriterion
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
            throw new stubIllegalArgumentException('SeachValue for LIKE criteria can not be NULL.');
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
        return 'LIKE';
    }
}
?>