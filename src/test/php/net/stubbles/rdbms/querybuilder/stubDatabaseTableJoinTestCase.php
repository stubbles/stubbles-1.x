<?php
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseTableJoin.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @version     $Id: stubDatabaseTableJoinTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseTableJoin');
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseTableJoin.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @group       rdbms
 * @group       rdbms_querybuilder
 */
class stubDatabaseTableJoinTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseTableJoin
     */
    protected $tableJoin;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->tableJoin = new stubDatabaseTableJoin();
    }

    /**
     * check that setting and getting the name works as expected
     *
     * @test
     */
    public function name()
    {
        $this->assertEquals('', $this->tableJoin->getName());
        $this->tableJoin->setName('foo');
        $this->assertEquals('foo', $this->tableJoin->getName());
    }

    /**
     * check that setting and getting the type works as expected
     *
     * @test
     */
    public function type()
    {
        $this->assertEquals($this->tableJoin->getType(), '');
        $this->tableJoin->setType('inner');
        $this->assertEquals('INNER', $this->tableJoin->getType());
        $this->tableJoin->setType('CrOsS');
        $this->assertEquals('CROSS', $this->tableJoin->getType());
        $this->tableJoin->setType('STRAIGHT');
        $this->assertEquals('STRAIGHT', $this->tableJoin->getType());
        $this->tableJoin->setType('NATURAL LEFT OUTER');
        $this->assertEquals('NATURAL LEFT OUTER', $this->tableJoin->getType());
        $this->tableJoin->setType('NATURAL RIGHT OUTER');
        $this->assertEquals('NATURAL RIGHT OUTER', $this->tableJoin->getType());
    }

    /**
     * check that setting and getting the type works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalType()
    {
        $this->tableJoin->setType('foo');
    }

    /**
     * check that setting and getting the condition type works as expected
     *
     * @test
     */
    public function conditionType()
    {
        $this->assertEquals($this->tableJoin->getConditionType(), '');
        $this->tableJoin->setConditionType('using');
        $this->assertEquals('USING', $this->tableJoin->getConditionType());
        $this->tableJoin->setConditionType('ON');
        $this->assertEquals('ON', $this->tableJoin->getConditionType());
    }

    /**
     * check that setting and getting the condition type works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalConditionType()
    {
        $this->tableJoin->setConditionType('foo');
    }

    /**
     * check that setting and getting the condition works as expected
     *
     * @test
     */
    public function condition()
    {
        $this->assertNull($this->tableJoin->getCondition());
        $this->assertFalse($this->tableJoin->hasCondition());
        $this->tableJoin->setCondition('foo');
        $this->assertEquals('foo', $this->tableJoin->getCondition());
        $this->assertTrue($this->tableJoin->hasCondition());
    }
}
?>