<?php
/**
 * Test for net::stubbles::rdbms::criteria::stubGreaterThanCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @version     $Id: stubGreaterThanCriterionTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubGreaterThanCriterion');
/**
 * Test for net::stubbles::rdbms::criteria::stubGreaterThanCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @group       rdbms
 * @group       rdbms_criteria
 */
class stubGreaterThanCriterionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * check that a null value throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function equalsNull()
    {
        $greaterThanCriterion = new stubGreaterThanCriterion('foo', null);
    }

    /**
     * check that any other value gets correct sql result
     *
     * @test
     */
    public function value()
    {
        $greaterThanCriterion = new stubGreaterThanCriterion('foo', 5);
        $this->assertEquals("`foo` > '5'", $greaterThanCriterion->toSQL());
        $greaterThanCriterion = new stubGreaterThanCriterion('foo', 6, 'baz');
        $this->assertEquals("`baz`.`foo` > '6'", $greaterThanCriterion->toSQL());
    }
}
?>