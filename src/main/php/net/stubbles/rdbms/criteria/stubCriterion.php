<?php
/**
 * Interface for criteria.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubCriterion.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Interface for criteria.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
interface stubCriterion extends stubObject
{
    /**
     * returns the criterion as sql
     * 
     * @return  string
     */
    public function toSQL();
}
?>