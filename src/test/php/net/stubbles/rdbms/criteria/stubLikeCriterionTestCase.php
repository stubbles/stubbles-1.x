<?php
/**
 * Test for net::stubbles::rdbms::criteria::stubLikeCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @version     $Id: stubLikeCriterionTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubLikeCriterion');
/**
 * Test for net::stubbles::rdbms::criteria::stubLikeCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @group       rdbms
 * @group       rdbms_criteria
 */
class stubLikeCriterionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * check that a null value throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function equalsNull()
    {
        $likeCriterion = new stubLikeCriterion('foo', null);
    }

    /**
     * check that any other value gets correct sql result
     */
    public function testValue()
    {
        $likeCriterion = new stubLikeCriterion('foo', '%bar');
        $this->assertEquals("`foo` LIKE '%bar'", $likeCriterion->toSQL());
        $likeCriterion = new stubLikeCriterion('foo', 'bar%', 'baz');
        $this->assertEquals("`baz`.`foo` LIKE 'bar%'", $likeCriterion->toSQL());
    }
}
?>