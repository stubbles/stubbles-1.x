<?php
/**
 * Test for net::stubbles::rdbms::criteria::stubNegateCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @version     $Id: stubNegateCriterionTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubNegateCriterion');
/**
 * Test for net::stubbles::rdbms::criteria::stubNegateCriterion.
 *
 * @package     stubbles
 * @subpackage  rdbms_criteria_test
 * @group       rdbms
 * @group       rdbms_criteria
 */
class stubNegateCriterionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * check that the given criterion is negated
     *
     * @test
     */
    public function negation()
    {
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->once())->method('toSQL')->will($this->returnValue('foo'));
        $negateCriterion = new stubNegateCriterion($mockCriterion);
        $this->assertEquals('NOT (foo)', $negateCriterion->toSQL());
    }
}
?>