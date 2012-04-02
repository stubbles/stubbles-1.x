<?php
/**
 * Test for net::stubbles::rdbms::criteria::stubInCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @version     $Id: stubInCriterionTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubInCriterion');
/**
 * Test for net::stubbles::rdbms::criteria::stubInCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @group       rdbms
 * @group       rdbms_criteria
 */
class stubInCriterionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * check that a non-array value throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function nonArray()
    {
        $inCriterion = new stubInCriterion('foo', 'bar');
    }

    /**
     * check that one value is handled correct
     *
     * @test
     */
    public function one()
    {
        $inCriterion = new stubInCriterion('foo', array('bar'));
        $this->assertEquals("`foo` IN ('bar')", $inCriterion->toSQL());
        $inCriterion = new stubInCriterion('foo', array('bar'), 'baz');
        $this->assertEquals("`baz`.`foo` IN ('bar')", $inCriterion->toSQL());
    }

    /**
     * check that two values are handled correct
     *
     * @test
     */
    public function two()
    {
        $inCriterion = new stubInCriterion('foo', array('bar', 'dummy'));
        $this->assertEquals("`foo` IN ('bar', 'dummy')", $inCriterion->toSQL());
        $inCriterion = new stubInCriterion('foo', array('bar', 'dummy'), 'baz');
        $this->assertEquals("`baz`.`foo` IN ('bar', 'dummy')", $inCriterion->toSQL());
    }
}
?>