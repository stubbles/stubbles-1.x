<?php
/**
 * Test for net::stubbles::rdbms::criteria::stubEqualCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @version     $Id: stubEqualCriterionTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubEqualCriterion');
/**
 * Test for net::stubbles::rdbms::criteria::stubEqualCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @group       rdbms
 * @group       rdbms_criteria
 */
class stubEqualCriterionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * check that a null value gets correct operator
     *
     * @test
     */
    public function equalsNull()
    {
        $equalCriterion = new stubEqualCriterion('foo', null);
        $this->assertEquals('`foo` IS NULL',$equalCriterion->toSQL());
        $equalCriterion = new stubEqualCriterion('foo', null, 'bar');
        $this->assertEquals('`bar`.`foo` IS NULL', $equalCriterion->toSQL());
    }

    /**
     * check that any other value gets correct operator
     *
     * @test
     */
    public function value()
    {
        $equalCriterion = new stubEqualCriterion('foo', 'bar');
        $this->assertEquals("`foo` = 'bar'", $equalCriterion->toSQL());
        $equalCriterion = new stubEqualCriterion('foo', 'bar', 'baz');
        $this->assertEquals("`baz`.`foo` = 'bar'", $equalCriterion->toSQL());
    }
}
?>