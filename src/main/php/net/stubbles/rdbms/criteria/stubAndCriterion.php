<?php
/**
 * Composition of several criteria connected by AND.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubAndCriterion.php 2145 2009-03-29 12:47:02Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubAbstractCompositeCriterion');
/**
 * Composition of several criteria connected by AND.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
class stubAndCriterion extends stubAbstractCompositeCriterion
{
    /**
     * static constructor
     *
     * @return  stubAndCriterion
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
        return 'AND';
    }
}
?>