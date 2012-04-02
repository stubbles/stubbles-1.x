<?php
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseMySQLQueryBuilder.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @version     $Id: stubDatabaseMySQLQueryBuilderTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseMySQLQueryBuilder');
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseMySQLQueryBuilder.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @group       rdbms
 * @group       rdbms_querybuilder
 */
class stubDatabaseMySQLQueryBuilderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseMySQLQueryBuilder
     */
    protected $mySqlQueryBuilder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mySqlQueryBuilder = new stubDatabaseMySQLQueryBuilder();
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function isMarkedAsSingleton()
    {
        $this->assertTrue($this->mySqlQueryBuilder->getClass()
                                                  ->hasAnnotation('Singleton')
        );
    }

    /**
     * test creating a select query
     *
     * @test
     */
    public function createSelect()
    {
        $tableDescription = new stubDatabaseTableDescription();
        $tableDescription->setName('foo');
        $select = new stubDatabaseSelect($tableDescription);
        $this->assertEquals($this->mySqlQueryBuilder->createSelect($select), 'SELECT * FROM `foo`');
        $mockCriterion1 = $this->getMock('stubCriterion');
        $mockCriterion1->expects($this->any())->method('toSQL')->will($this->returnValue("`foo`.`id` = 'mock'"));
        $select->addCriterion($mockCriterion1);
        $this->assertEquals("SELECT * FROM `foo` WHERE (`foo`.`id` = 'mock')", $this->mySqlQueryBuilder->createSelect($select));
        $tableJoin1 = new stubDatabaseTableJoin();
        $tableJoin1->setName('bar');
        $tableJoin1->setType('INNER');
        $tableJoin1->setConditionType('USING');
        $tableJoin1->setCondition('`id`');
        $select->addJoin($tableJoin1);
        $this->assertEquals("SELECT * FROM `foo` INNER JOIN `bar` USING (`id`) WHERE (`foo`.`id` = 'mock')", $this->mySqlQueryBuilder->createSelect($select));
        $tableJoin2 = new stubDatabaseTableJoin();
        $tableJoin2->setName('baz');
        $tableJoin2->setType('LEFT');
        $tableJoin2->setConditionType('ON');
        $tableJoin2->setCondition('`bar`.`id` = `baz`.`other_id`');
        $select->addJoin($tableJoin2);
        $mockCriterion2 = $this->getMock('stubCriterion');
        $mockCriterion2->expects($this->any())->method('toSQL')->will($this->returnValue("`baz`.`other_id` IS NOT NULL"));
        $select->addCriterion($mockCriterion2);
        $this->assertEquals("SELECT * FROM `foo` INNER JOIN `bar` USING (`id`) LEFT JOIN `baz` ON `bar`.`id` = `baz`.`other_id` WHERE (`foo`.`id` = 'mock' AND `baz`.`other_id` IS NOT NULL)", $this->mySqlQueryBuilder->createSelect($select));
        $select->orderBy('foo ASC');
        $this->assertEquals("SELECT * FROM `foo` INNER JOIN `bar` USING (`id`) LEFT JOIN `baz` ON `bar`.`id` = `baz`.`other_id` WHERE (`foo`.`id` = 'mock' AND `baz`.`other_id` IS NOT NULL) ORDER BY foo ASC", $this->mySqlQueryBuilder->createSelect($select));
        $select->limitBy(50, 10);
        $this->assertEquals("SELECT * FROM `foo` INNER JOIN `bar` USING (`id`) LEFT JOIN `baz` ON `bar`.`id` = `baz`.`other_id` WHERE (`foo`.`id` = 'mock' AND `baz`.`other_id` IS NOT NULL) ORDER BY foo ASC LIMIT 50,10", $this->mySqlQueryBuilder->createSelect($select));
    }

    /**
     * test creating an insert query
     *
     * @test
     */
    public function createInsert()
    {
        $tableRow1 = new stubDatabaseTableRow('foo');
        $tableRow1->setColumn('columnName1', "column'Value1");
        $tableRow1->setColumn('columnName2', null);
        $tableRow1->setColumn('columnName3', 313);
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->never())->method('toSQL');
        $tableRow1->addCriterion($mockCriterion);
        $this->assertEquals(array('foo' => "INSERT INTO `foo` (`columnName1`, `columnName2`, `columnName3`) VALUES ('column\'Value1', NULL, 313)"), $this->mySqlQueryBuilder->createInsert(array('foo' => $tableRow1)));
        
        $tableRow2 = new stubDatabaseTableRow('bar');
        $tableRow2->setColumn('columnNameA', 'columnValueA');
        $this->assertEquals(array('foo' => "INSERT INTO `foo` (`columnName1`, `columnName2`, `columnName3`) VALUES ('column\'Value1', NULL, 313)",
                                  'bar' => "INSERT INTO `bar` (`columnNameA`) VALUES ('columnValueA')"
                            ),
                            $this->mySqlQueryBuilder->createInsert(array('foo' => $tableRow1,
                                                                         'bar' => $tableRow2
                                                                   )
                            )
        );
    }

    /**
     * test creating an insert query
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function createInsertWithWrongArgument()
    {
        $this->mySqlQueryBuilder->createInsert(array('foo' => new stdClass()));
    }

    /**
     * test creating an update query
     *
     * @test
     */
    public function createUpdate()
    {
        $tableRow1 = new stubDatabaseTableRow('foo');
        $tableRow1->setColumn('columnName1', "column'Value1");
        $tableRow1->setColumn('columnName2', null);
        $tableRow1->setColumn('columnName3', 313);
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->any())->method('toSQL')->will($this->returnValue("`foo`.`id` = 'mock'"));
        $tableRow1->addCriterion($mockCriterion);
        $this->assertEquals($this->mySqlQueryBuilder->createUpdate(array('foo' => $tableRow1)), array('foo' => "UPDATE `foo` SET `columnName1` = 'column\'Value1', `columnName2` = NULL, `columnName3` = 313 WHERE (`foo`.`id` = 'mock')"));
        
        $tableRow2 = new stubDatabaseTableRow('bar');
        $tableRow2->setColumn('columnNameA', 'columnValueA');
        $this->assertEquals($this->mySqlQueryBuilder->createUpdate(array('foo' => $tableRow1,
                                                                         'bar' => $tableRow2
                                                                  )
                           ),
                           array('foo' => "UPDATE `foo` SET `columnName1` = 'column\'Value1', `columnName2` = NULL, `columnName3` = 313 WHERE (`foo`.`id` = 'mock')",
                                 'bar' => "UPDATE `bar` SET `columnNameA` = 'columnValueA'"
                           )
        );
    }

    /**
     * test creating an update query
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function createUpdateWrongArgument()
    {
        $this->mySqlQueryBuilder->createUpdate(array('foo' => new stdClass()));
    }

    /**
     * test creating a delete query
     *
     * @test
     */
    public function createDelete()
    {
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->any())->method('toSQL')->will($this->returnValue("`foo`.`id` = 'mock'"));
        $this->assertEquals("DELETE FROM `bar` WHERE `foo`.`id` = 'mock'", $this->mySqlQueryBuilder->createDelete('bar', $mockCriterion));
    }

    /**
     * test creating a create table query
     *
     * @test
     * @expectedException  stubDatabaseQueryBuilderException
     */
    public function createTableWithoutColumns()
    {
        $tableDescription = new stubDatabaseTableDescription();
        $tableDescription->setName('foo');
        $tableDescription->setType('InnoDB');
        $this->mySqlQueryBuilder->createTable($tableDescription);
    }

    /**
     * test creating a create table query
     *
     * @test
     */
    public function createTable()
    {
        $tableDescription = new stubDatabaseTableDescription();
        $tableDescription->setName('foo');
        $tableDescription->setType('InnoDB');
        $column1 = new stubDatabaseTableColumn();
        $column1->setName('column1');
        $column1->setType('TEXT');
        $column1->setSize(50);
        $column1->setIsNullable(true);
        $tableDescription->addColumn($column1);
        $this->assertEquals("CREATE TABLE `foo` (\n" .
                            "  column1 TEXT DEFAULT NULL\n" .
                            ") ENGINE = InnoDB",
                            $this->mySqlQueryBuilder->createTable($tableDescription));
        $column1->setCharacterSet('utf8');
        $column1->setCollation('utf8_general_ci');
        $tableDescription->setCharacterSet('utf8');
        $tableDescription->setCollation('utf8_general_ci');
        $tableDescription->setComment('this is a comment');
        $this->assertEquals("CREATE TABLE `foo` (\n" .
                            "  column1 TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL\n" .
                            ") ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'this is a comment'",
                            $this->mySqlQueryBuilder->createTable($tableDescription));
    }

    /**
     * test creating a create table query
     *
     * @test
     */
    public function createTableWithDifferentColumns()
    {
        $tableDescription = new stubDatabaseTableDescription();
        $tableDescription->setName('foo');
        $tableDescription->setType('InnoDB');
        $column1 = new stubDatabaseTableColumn();
        $column1->setName('column1');
        $column1->setType('VARCHAR');
        $column1->setSize(50);
        $column1->setIsNullable(false);
        $column1->setCharacterSet('utf8');
        $column1->setCollation('utf8_general_ci');
        $tableDescription->addColumn($column1);
        
        $column2 = new stubDatabaseTableColumn();
        $column2->setName('column2');
        $column2->setType('MEDIUMINT');
        $column2->setSize(6);
        $column2->setIsPrimaryKey(true);
        $column2->setCharacterSet('utf8');
        $column2->setCollation('utf8_general_ci');
        $tableDescription->addColumn($column2);
        
        $column3 = new stubDatabaseTableColumn();
        $column3->setName('column3');
        $column3->setType('ENUM');
        $column3->setSize("'1','2','3'");
        $column3->setIsKey(true);
        $column3->setDefaultValue('1');
        $column3->setCharacterSet('utf8');
        $column3->setCollation('utf8_general_ci');
        $tableDescription->addColumn($column3);
        
        $column4 = new stubDatabaseTableColumn();
        $column4->setName('column4');
        $column4->setType('DATETIME');
        $column4->setIsUnique(true);
        $tableDescription->addColumn($column4);
        
        $column5 = new stubDatabaseTableColumn();
        $column5->setName('column5');
        $column5->setType('DATE');
        $tableDescription->addColumn($column5);
        
        $column6 = new stubDatabaseTableColumn();
        $column6->setName('column6');
        $column6->setType('TIME');
        $tableDescription->addColumn($column6);
        
        $column7 = new stubDatabaseTableColumn();
        $column7->setName('column7');
        $column7->setType('TIMESTAMP');
        $tableDescription->addColumn($column7);
        
        $column8 = new stubDatabaseTableColumn();
        $column8->setName('column8');
        $column8->setType('YEAR');
        $tableDescription->addColumn($column8);
        
        $this->assertEquals("CREATE TABLE `foo` (\n" .
                            "  column1 VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,\n" .
                            "  column2 MEDIUMINT(6) NOT NULL AUTO_INCREMENT,\n" .
                            "  column3 ENUM('1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '1',\n" .
                            "  column4 DATETIME DEFAULT NULL,\n" .
                            "  column5 DATE DEFAULT NULL,\n" .
                            "  column6 TIME DEFAULT NULL,\n" .
                            "  column7 TIMESTAMP DEFAULT NULL,\n" .
                            "  column8 YEAR DEFAULT NULL,\n" .
                            "  PRIMARY KEY (`column2`),\n" .
                            "  KEY (`column3`),\n" .
                            "  UNIQUE (`column4`)\n" .
                            ") ENGINE = InnoDB",
                            $this->mySqlQueryBuilder->createTable($tableDescription));
    }

    /**
     * test creating a create table query
     *
     * @test
     */
    public function createTableWithMultiplePrimaryKey()
    {
        $tableDescription = new stubDatabaseTableDescription();
        $tableDescription->setName('foo');
        $tableDescription->setType('InnoDB');
        $column1 = new stubDatabaseTableColumn();
        $column1->setName('column1');
        $column1->setType('MEDIUMINT');
        $column1->setSize(6);
        $column1->setIsPrimaryKey(true);
        $tableDescription->addColumn($column1);
        
        $column2 = new stubDatabaseTableColumn();
        $column2->setName('column2');
        $column2->setType('MEDIUMINT');
        $column2->setSize(6);
        $column2->setIsPrimaryKey(true);
        $tableDescription->addColumn($column2);
        
        $this->assertEquals("CREATE TABLE `foo` (\n" .
                            "  column1 MEDIUMINT(6) NOT NULL,\n" .
                            "  column2 MEDIUMINT(6) NOT NULL,\n" .
                            "  PRIMARY KEY (`column1`, `column2`)\n" .
                            ") ENGINE = InnoDB", $this->mySqlQueryBuilder->createTable($tableDescription));
    }
}
?>