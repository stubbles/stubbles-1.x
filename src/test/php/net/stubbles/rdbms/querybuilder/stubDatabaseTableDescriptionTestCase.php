<?php
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseTableDescription.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @version     $Id: stubDatabaseTableDescriptionTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseTableDescription');
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseTableDescription.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @group       rdbms
 * @group       rdbms_querybuilder
 */
class stubDatabaseTableDescriptionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseTableDescription
     */
    protected $tableDescription;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->tableDescription = stubDatabaseTableDescription::create();
    }

    /**
     * @test
     */
    public function nameIsEmptyBeDefault()
    {
        $this->assertEquals('', $this->tableDescription->getName());
    }

    /**
     * @test
     */
    public function nameCanBeSet()
    {
        $this->assertEquals('foo',
                            $this->tableDescription->setName('foo')
                                                   ->getName()
        );
    }

    /**
     * @test
     */
    public function hasNoTypeBeDefault()
    {
        $this->assertNull($this->tableDescription->getType());
        $this->assertFalse($this->tableDescription->hasType());
    }

    /**
     * @test
     */
    public function typeCanBeSet()
    {
        $this->assertEquals('foo',
                            $this->tableDescription->setType('foo')
                                                   ->getType()
        );
        $this->assertTrue($this->tableDescription->hasType());
    }

    /**
     * @test
     */
    public function hasNoCharacterSetByDefault()
    {
        $this->assertNull($this->tableDescription->getCharacterSet());
        $this->assertFalse($this->tableDescription->hasCharacterSet());
    }

    /**
     * @test
     */
    public function characterSetCanBeSet()
    {
        $this->assertEquals('utf-8',
                            $this->tableDescription->setCharacterSet('utf-8')
                                                   ->getCharacterSet()
        );
        $this->assertTrue($this->tableDescription->hasCharacterSet());
    }

    /**
     * @test
     */
    public function hasNoCollationByDefault()
    {
        $this->assertNull($this->tableDescription->getCollation());
        $this->assertFalse($this->tableDescription->hasCollation());
    }

    /**
     * @test
     */
    public function collationCanBeSet()
    {
        $this->assertEquals('utf-8',
                            $this->tableDescription->setCollation('utf-8')
                                                   ->getCollation()
        );
        $this->assertTrue($this->tableDescription->hasCollation());
    }

    /**
     * @test
     */
    public function hasNoCommentByDefault()
    {
        $this->assertNull($this->tableDescription->getComment());
    }

    /**
     * @test
     */
    public function commentCanBeSet()
    {
        $this->assertEquals('foo',
                            $this->tableDescription->setComment('foo')
                                                   ->getComment()
        );
    }

    /**
     * @test
     */
    public function addColumnsWithoutOwnOrder()
    {
        $column1 = new stubDatabaseTableColumn();
        $column1->setName('foo');
        $this->tableDescription->addColumn($column1);
        $column2 = new stubDatabaseTableColumn();
        $column2->setName('bar');
        $this->tableDescription->addColumn($column2);
        $column3 = new stubDatabaseTableColumn();
        $column3->setName('baz');
        $this->tableDescription->addColumn($column3);
        $this->assertEquals(array(1 => $column1,
                                  2 => $column2,
                                  3 => $column3
                            ),
                            $this->tableDescription->getColumns()
        );
    }

    /**
     * @test
     */
    public function addColumnsWithOwnOrder()
    {
        $column1 = new stubDatabaseTableColumn();
        $column1->setName('foo');
        $column1->setOrder(2);
        $this->tableDescription->addColumn($column1);
        $column2 = new stubDatabaseTableColumn();
        $column2->setName('bar');
        $column2->setOrder(4);
        $this->tableDescription->addColumn($column2);
        $column3 = new stubDatabaseTableColumn();
        $column3->setName('baz');
        $column3->setOrder(1);
        $this->tableDescription->addColumn($column3);
        $this->assertEquals(array(1 => $column3,
                                  2 => $column1,
                                  4 => $column2,
                            ),
                            $this->tableDescription->getColumns()
        );
    }

    /**
     * @test
     * @expectedException  stubDatabaseException
     */
    public function addColumnsWithOwnOrderWithTwoColumnsOfSameOrderThrowsDatabaseException()
    {
        $column1 = new stubDatabaseTableColumn();
        $column1->setName('foo');
        $column1->setOrder(2);
        $this->tableDescription->addColumn($column1);
        $column2 = new stubDatabaseTableColumn();
        $column2->setName('bar');
        $column2->setOrder(4);
        $this->tableDescription->addColumn($column2);
        $column3 = new stubDatabaseTableColumn();
        $column3->setName('baz');
        $column3->setOrder(2);
        $this->tableDescription->addColumn($column3);
    }

    /**
     * @test
     * @expectedException  stubDatabaseException
     */
    public function addColumnsWithAndWithoutOwnOrderWithTwoColumnsOfSameOrderThrowsDatabaseException()
    {
        $column1 = new stubDatabaseTableColumn();
        $column1->setName('foo');
        $this->tableDescription->addColumn($column1);
        $column2 = new stubDatabaseTableColumn();
        $column2->setName('bar');
        $this->tableDescription->addColumn($column2);
        $column3 = new stubDatabaseTableColumn();
        $column3->setName('baz');
        $column3->setOrder(2);
        $this->tableDescription->addColumn($column3);
    }

    /**
     * @test
     * @expectedException  stubDatabaseException
     */
    public function adColumnsWithTwoColumnsOfSameNameThrowsDatabaseException()
    {
        $column1 = new stubDatabaseTableColumn();
        $column1->setName('foo');
        $this->tableDescription->addColumn($column1);
        $column2 = new stubDatabaseTableColumn();
        $column2->setName('bar');
        $this->tableDescription->addColumn($column2);
        $column3 = new stubDatabaseTableColumn();
        $column3->setName('foo');
        $this->tableDescription->addColumn($column3);
    }
}
?>