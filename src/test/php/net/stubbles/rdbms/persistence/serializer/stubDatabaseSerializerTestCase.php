<?php
/**
 * Test for net::stubbles::rdbms::persistence::serializer::stubDatabaseSerializer.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_serializer_test
 * @version     $Id: stubDatabaseSerializerTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::persistence::serializer::stubDatabaseSerializer');
require_once dirname(__FILE__) . '/../../querybuilder/TeststubDatabaseQueryBuilder.php';
require_once dirname(__FILE__) . '/../MockNoEntityAnnotationEntity.php';
require_once dirname(__FILE__) . '/../MockSinglePrimaryKeyEntity.php';
/**
 * Test for net::stubbles::rdbms::persistence::serializer::stubDatabaseSerializer.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_serializer_test
 * @group       rdbms
 * @group       rdbms_persistence
 */
class stubDatabaseSerializerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseSerializer
     */
    protected $dbSerializer;
    /**
     * mock for pdo
     *
     * @var  SimpleMock
     */
    protected $mockConnection;
    /**
     * a test query builder
     *
     * @var  TeststubDatabaseQueryBuilder
     */
    protected $mockQueryBuilder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockConnection = $this->getMock('stubDatabaseConnection');
        $this->mockConnection->expects($this->any())->method('getDatabase')->will($this->returnValue('mock'));
        $this->mockQueryBuilder      = new TeststubDatabaseQueryBuilder();
        $databaseQueryBuilderProvider = $this->getMock('stubDatabaseQueryBuilderProvider', array(), array(), '', false);
        $databaseQueryBuilderProvider->expects($this->any())
                                     ->method('create')
                                     ->will($this->returnValue($this->mockQueryBuilder));
        $this->dbSerializer = new stubDatabaseSerializer($databaseQueryBuilderProvider);
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function isMarkedAsSingleton()
    {
        $this->assertTrue($this->dbSerializer->getClass()->hasAnnotation('Singleton'));
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->dbSerializer->getClass()
                                             ->getConstructor()
                                             ->hasAnnotation('Inject')
        );
    }

    /**
     * test that trying to find a class that does not have an entity annotation throws an exception
     *
     * @test
     * @expectedException  stubPersistenceException
     */
    public function insertNonEntity()
    {
        $this->dbSerializer->insert($this->mockConnection, new MockNoEntityAnnotationEntity());
    }

    /**
     * test that trying to find a class that does not have an entity annotation throws an exception
     *
     * @test
     * @expectedException  stubPersistenceException
     */
    public function updateNonEntity()
    {
        $this->dbSerializer->update($this->mockConnection, new MockNoEntityAnnotationEntity());
    }

    /**
     * test that trying to find a class that does not have an entity annotation throws an exception
     *
     * @test
     * @expectedException  stubPersistenceException
     */
    public function serializeNonEntity()
    {
        $this->dbSerializer->serialize($this->mockConnection, new MockNoEntityAnnotationEntity());
    }

    /**
     * test insert with a single primary key
     *
     * @test
     */
    public function insertWithSinglePrimaryKey()
    {
        $singlePrimaryKeyEntity = new MockSinglePrimaryKeyEntity();
        $this->mockConnection->expects($this->any())->method('getLastInsertId')->will($this->returnValue('mockId'));
        $this->mockConnection->expects($this->once())->method('exec');
        $this->mockQueryBuilder->setInsertQueries(array('foo' => 'foo'));
        $this->assertEquals(stubDatabaseSerializer::INSERT, $this->dbSerializer->insert($this->mockConnection, $singlePrimaryKeyEntity));
        $this->assertEquals(1, $this->mockQueryBuilder->getCallCount('createInsert'));
        $this->assertEquals(0, $this->mockQueryBuilder->getCallCount('createUpdate'));
        $tableRows = $this->mockQueryBuilder->getInsertTableRows();
        $this->assertEquals(1, count($tableRows));
        $this->assertTrue(isset($tableRows['foo']));
        $this->assertEquals('mockId', $singlePrimaryKeyEntity->getId());
        $this->assertEquals(array('bar' => 'this is bar', 'default' => 'example', 'date' => null), $tableRows['foo']->getColumns());
        $this->assertFalse($tableRows['foo']->hasCriterion());
    }

    /**
     * test insert with a single primary key
     *
     * @test
     */
    public function insertWithSinglePrimaryKeyAlreadySet()
    {
        $singlePrimaryKeyEntity = new MockSinglePrimaryKeyEntity();
        $singlePrimaryKeyEntity->setId('mockId');
        $this->mockConnection->expects($this->never())->method('getLastInsertId');
        $this->mockConnection->expects($this->once())->method('exec');
        $this->mockQueryBuilder->setInsertQueries(array('foo' => 'foo'));
        $this->assertEquals(stubDatabaseSerializer::INSERT, $this->dbSerializer->insert($this->mockConnection, $singlePrimaryKeyEntity));
        $this->assertEquals(1, $this->mockQueryBuilder->getCallCount('createInsert'));
        $this->assertEquals(0, $this->mockQueryBuilder->getCallCount('createUpdate'));
        $tableRows = $this->mockQueryBuilder->getInsertTableRows();
        $this->assertEquals(1, count($tableRows));
        $this->assertTrue(isset($tableRows['foo']));
        $this->assertEquals('mockId', $singlePrimaryKeyEntity->getId());
        $this->assertEquals(array('id' => 'mockId', 'bar' => 'this is bar', 'default' => 'example', 'date' => null), $tableRows['foo']->getColumns());
        $this->assertFalse($tableRows['foo']->hasCriterion());
    }

    /**
     * test update with a single primary key
     *
     * @test
     */
    public function updateWithSinglePrimaryKey()
    {
        $singlePrimaryKeyEntity = new MockSinglePrimaryKeyEntity();
        $singlePrimaryKeyEntity->setId('mockId');
        $singlePrimaryKeyEntity->setDefaultValue('anotherExample');
        $singlePrimaryKeyEntity->setDate(new stubDate('2008-10-23 19:27:22'));
        $this->mockConnection->expects($this->never())->method('getLastInsertId');
        $this->mockConnection->expects($this->once())->method('exec');
        $this->mockQueryBuilder->setUpdateQueries(array('foo' => 'foo'));
        $this->assertEquals(stubDatabaseSerializer::UPDATE, $this->dbSerializer->update($this->mockConnection, $singlePrimaryKeyEntity));
        $this->assertEquals(0, $this->mockQueryBuilder->getCallCount('createInsert'));
        $this->assertEquals(1, $this->mockQueryBuilder->getCallCount('createUpdate'));
        $tableRows = $this->mockQueryBuilder->getUpdateTableRows();
        $this->assertEquals(1, count($tableRows));
        $this->assertTrue(isset($tableRows['foo']));
        $this->assertEquals('mockId', $singlePrimaryKeyEntity->getId());
        $this->assertEquals(array('bar' => 'this is bar', 'default' => 'anotherExample', 'date' => '2008-10-23 19:27:22'), $tableRows['foo']->getColumns());
        $this->assertTrue($tableRows['foo']->hasCriterion());
        $this->assertEquals("(`foo`.`id` = 'mockId')", $tableRows['foo']->getCriterion()->toSQL());
    }

    /**
     * test insert with a single primary key
     *
     * @test
     */
    public function serializeInsertWithSinglePrimaryKey()
    {
        $singlePrimaryKeyEntity = new MockSinglePrimaryKeyEntity();
        $this->mockConnection->expects($this->any())->method('getLastInsertId')->will($this->returnValue('mockId'));
        $this->mockConnection->expects($this->once())->method('exec');
        $this->mockQueryBuilder->setInsertQueries(array('foo' => 'foo'));
        $this->assertEquals(stubDatabaseSerializer::INSERT, $this->dbSerializer->serialize($this->mockConnection, $singlePrimaryKeyEntity));
        $this->assertEquals(1, $this->mockQueryBuilder->getCallCount('createInsert'));
        $this->assertEquals(0, $this->mockQueryBuilder->getCallCount('createUpdate'));
        $tableRows = $this->mockQueryBuilder->getInsertTableRows();
        $this->assertEquals(1, count($tableRows));
        $this->assertTrue(isset($tableRows['foo']));
        $this->assertEquals('mockId', $singlePrimaryKeyEntity->getId());
        $this->assertEquals(array('bar' => 'this is bar', 'default' => 'example', 'date' => null), $tableRows['foo']->getColumns());
        $this->assertFalse($tableRows['foo']->hasCriterion());
    }

    /**
     * test update with a single primary key
     *
     * @test
     */
    public function serializeUpdateWithSinglePrimaryKey()
    {
        $singlePrimaryKeyEntity = new MockSinglePrimaryKeyEntity();
        $singlePrimaryKeyEntity->setId('mockId');
        $singlePrimaryKeyEntity->setDefaultValue('anotherExample');
        $singlePrimaryKeyEntity->setDate(new stubDate('2008-10-23 19:27:22'));
        $this->mockConnection->expects($this->never())->method('getLastInsertId');
        $this->mockConnection->expects($this->once())->method('exec');
        $this->mockQueryBuilder->setUpdateQueries(array('foo' => 'foo'));
        $this->assertEquals(stubDatabaseSerializer::UPDATE, $this->dbSerializer->serialize($this->mockConnection, $singlePrimaryKeyEntity));
        $this->assertEquals(0, $this->mockQueryBuilder->getCallCount('createInsert'));
        $this->assertEquals(1, $this->mockQueryBuilder->getCallCount('createUpdate'));
        $tableRows = $this->mockQueryBuilder->getUpdateTableRows();
        $this->assertEquals(1, count($tableRows));
        $this->assertTrue(isset($tableRows['foo']));
        $this->assertEquals('mockId', $singlePrimaryKeyEntity->getId());
        $this->assertEquals(array('bar' => 'this is bar', 'default' => 'anotherExample', 'date' => '2008-10-23 19:27:22'), $tableRows['foo']->getColumns());
        $this->assertTrue($tableRows['foo']->hasCriterion());
        $this->assertEquals("(`foo`.`id` = 'mockId')", $tableRows['foo']->getCriterion()->toSQL());
    }
}
?>