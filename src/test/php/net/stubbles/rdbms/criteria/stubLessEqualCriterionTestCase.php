<?php
/**
 * Test for net::stubbles::rdbms::criteria::stubLessEqualCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @version     $Id: stubLessEqualCriterionTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 *
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubLessEqualCriterion');
/**
 * Test for net::stubbles::rdbms::criteria::stubLessEqualCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @group       rdbms
 * @group       rdbms_criteria
 */
class stubLessEqualCriterionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * check that a null value throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function equalsNull()
    {
        $lessEqualCriterion = new stubLessEqualCriterion('foo', null);
    }

    /**
     * check that any other value gets correct sql result
     *
     * @test
     */
    public function value()
    {
        $lessEqualCriterion = new stubLessEqualCriterion('foo', 5);
        $this->assertEquals("`foo` <= '5'", $lessEqualCriterion->toSQL());
        $lessEqualCriterion = new stubLessEqualCriterion('foo', 6, 'baz');
        $this->assertEquals("`baz`.`foo` <= '6'", $lessEqualCriterion->toSQL());
    }
}
?>