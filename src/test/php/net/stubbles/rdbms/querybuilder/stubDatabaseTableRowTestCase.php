<?php
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseTableRow.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @version     $Id: stubDatabaseTableRowTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseTableRow');
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseTableRow.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @group       rdbms
 * @group       rdbms_querybuilder
 */
class stubDatabaseTableRowTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseTableRow
     */
    protected $tableRow;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->tableRow = new stubDatabaseTableRow('foo');
    }

    /**
     * check that getting the table name works as expected
     *
     * @test
     */
    public function tableName()
    {
        $this->assertEquals('foo', $this->tableRow->getTableName());
    }

    /**
     * check that joins are handled as expected
     *
     * @test
     */
    public function columns()
    {
        $this->assertEquals(array(), $this->tableRow->getColumns());
        $this->assertEquals(array(), $this->tableRow->getColumnNames());
        $this->tableRow->setColumn('bar', 'baz');
        $this->assertEquals(array('bar' => 'baz'), $this->tableRow->getColumns());
        $this->assertEquals(array('bar'), $this->tableRow->getColumnNames());
    }

    /**
     * test that criterions are handled as expected
     *
     * @test
     */
    public function criterion()
    {
        $this->assertFalse($this->tableRow->hasCriterion());
        $mockCriterion = $this->getMock('stubCriterion');
        $this->tableRow->addCriterion($mockCriterion);
        $this->assertTrue($this->tableRow->hasCriterion());
    }
}
?>