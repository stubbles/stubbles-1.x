<?php
/**
 * Criterion to negate another criterion.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 * @version     $Id: stubNegateCriterion.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubCriterion');
/**
 * Criterion to negate another criterion.
 * 
 * @package     stubbles
 * @subpackage  rdbms_criteria
 */
class stubNegateCriterion extends stubBaseObject implements stubCriterion
{
    /**
     * the criterion that should be negated
     *
     * @var  stubCriterion
     */
    protected $criterion;
    
    /**
     * constructor
     *
     * @param  stubCriterion  $criterion  criterion that should be negated
     */
    public function __construct(stubCriterion $criterion)
    {
        $this->criterion = $criterion;
    }
    
    /**
     * returns the criterion as sql
     *
     * @return  string
     */
    public function toSQL()
    {
        return 'NOT (' . $this->criterion->toSQL() . ')';
    }
}
?>