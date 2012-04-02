<?php
/**
 * Composition of several criteria connected by OR.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubOrCriterion.php 2145 2009-03-29 12:47:02Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubAbstractCompositeCriterion');
/**
 * Composition of several criteria connected by OR.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
class stubOrCriterion extends stubAbstractCompositeCriterion
{
    /**
     * static constructor
     *
     * @return  stubOrCriterion
     */
    public static function create()
    {
        return new self();
    }

    /**
     * returns the the operator to connect the criteria
     *
     * @return  string
     */
    protected function getOperator()
    {
        return 'OR';
    }
}
?>