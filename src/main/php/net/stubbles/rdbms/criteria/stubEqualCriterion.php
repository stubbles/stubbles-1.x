<?php
/**
 * Criterion to check for equality.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubEqualCriterion.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubAbstractCriterion');
/**
 * Criterion to check for equality.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
class stubEqualCriterion extends stubAbstractCriterion
{
    /**
     * returns the operator of the criterion
     *
     * @return  string
     */
    protected function getOperator()
    {
        if (null !== $this->searchValue) {
            return '=';
        }
        
        return 'IS';
    }
    
    /**
     * returns the the search value
     *
     * @return  string
     */
    protected function getSearchValue()
    {
        if (null !== $this->searchValue) {
            return "'" . $this->searchValue . "'";
        }
        
        return 'NULL';
    }
}
?>