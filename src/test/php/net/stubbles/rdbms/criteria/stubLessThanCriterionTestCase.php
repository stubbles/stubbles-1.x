<?php
/**
 * Test for net::stubbles::rdbms::criteria::stubLessThanCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @version     $Id: stubLessThanCriterionTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubLessThanCriterion');
/**
 * Test for net::stubbles::rdbms::criteria::stubLessThanCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @group       rdbms
 * @group       rdbms_criteria
 */
class stubLessThanCriterionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * check that a null value throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function equalsNull()
    {
        $lessThanCriterion = new stubLessThanCriterion('foo', null);
    }

    /**
     * check that any other value gets correct sql result
     *
     * @test
     */
    public function value()
    {
        $lessThanCriterion = new stubLessThanCriterion('foo', 5);
        $this->assertEquals("`foo` < '5'", $lessThanCriterion->toSQL());
        $lessThanCriterion = new stubLessThanCriterion('foo', 6, 'baz');
        $this->assertEquals("`baz`.`foo` < '6'", $lessThanCriterion->toSQL());
    }
}
?>