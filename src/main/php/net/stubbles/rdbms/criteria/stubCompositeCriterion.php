<?php
/**
 * Interface for a composition of several criteria.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubCompositeCriterion.php 2145 2009-03-29 12:47:02Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubCriterion');
/**
 * Interface for a composition of several criteria.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
interface stubCompositeCriterion extends stubCriterion
{
    /**
     * add a criterion to the composition
     *
     * @param   stubCriterion           $criterion
     * @return  stubCompositeCriterion
     */
    public function addCriterion(stubCriterion $criterion);
}
?>