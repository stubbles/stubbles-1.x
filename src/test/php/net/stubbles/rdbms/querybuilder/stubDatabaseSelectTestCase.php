<?php
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseSelect.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @version     $Id: stubDatabaseSelectTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseSelect');
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseSelect.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @group       rdbms
 * @group       rdbms_querybuilder
 */
class stubDatabaseSelectTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseSelect
     */
    protected $select;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $tableDescription = new stubDatabaseTableDescription();
        $tableDescription->setName('foo');
        $this->select = new stubDatabaseSelect($tableDescription);
    }

    /**
     * check that getting the base table name works as expected
     *
     * @test
     */
    public function baseTableName()
    {
        $this->assertEquals('foo', $this->select->getBaseTableName());
    }

    /**
     * check that joins are handled as expected
     *
     * @test
     */
    public function joins()
    {
        $this->assertFalse($this->select->hasJoins());
        $this->assertEquals(array(), $this->select->getJoins());
        $tableJoin = new stubDatabaseTableJoin();
        $tableJoin->setName('bar');
        $this->select->addJoin($tableJoin);
        $this->assertTrue($this->select->hasJoins());
        $this->assertEquals(array('bar' => $tableJoin),$this->select->getJoins());
    }

    /**
     * test that criterions are handled as expected
     *
     * @test
     */
    public function criterion()
    {
        $this->assertFalse($this->select->hasCriterion());
        $mockCriterion = $this->getMock('stubCriterion');
        $this->select->addCriterion($mockCriterion);
        $this->assertTrue($this->select->hasCriterion());
    }

    /**
     * orderedBy property
     *
     * @test
     */
    public function orderedBy()
    {
        $this->assertFalse($this->select->isOrdered());
        $this->assertNull($this->select->getOrderedBy());
        $this->assertSame($this->select, $this->select->orderBy('foo ASC'));
        $this->assertTrue($this->select->isOrdered());
        $this->assertEquals('foo ASC', $this->select->getOrderedBy());
    }

    /**
     * limit clause properties
     *
     * @test
     */
    public function limitClause()
    {
        $this->assertFalse($this->select->hasLimit());
        $this->assertNull($this->select->getOffset());
        $this->assertNull($this->select->getAmount());
        $this->assertSame($this->select, $this->select->limitBy(50, 10));
        $this->assertTrue($this->select->hasLimit());
        $this->assertEquals(50, $this->select->getOffset());
        $this->assertEquals(10, $this->select->getAmount());
        $this->assertSame($this->select, $this->select->limitBy(null, null));
        $this->assertFalse($this->select->hasLimit());
        $this->assertNull($this->select->getOffset());
        $this->assertNull($this->select->getAmount());
    }

}
?>