<?php
/**
 * Test for net::stubbles::rdbms::persistence::eraser::stubDatabaseEraser.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_eraser_test
 * @version     $Id: stubDatabaseEraserTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::persistence::eraser::stubDatabaseEraser',
                      'net::stubbles::rdbms::stubDatabaseResult'
);
require_once dirname(__FILE__) . '/../../querybuilder/TeststubDatabaseQueryBuilder.php';
require_once dirname(__FILE__) . '/../MockNoEntityAnnotationEntity.php';
require_once dirname(__FILE__) . '/../MockNoTableAnnotationEntity.php';
require_once dirname(__FILE__) . '/../MockSinglePrimaryKeyEntity.php';
/**
 * mock entity to test an entity that has no primary keys
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_eraser_test
 * @Entity
 */
class MockNoPrimaryKeyEntity extends stubBaseObject
{
    public function getFoo() { return 'foo'; }
}
/**
 * Test for net::stubbles::rdbms::persistence::eraser::stubDatabaseEraser.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_erasertest
 * @group       rdbms
 * @group       rdbms_persistence
 */
class stubDatabaseEraserTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseEraser
     */
    protected $dbEraser;
    /**
     * mock for pdo
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
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
        $this->dbEraser = new stubDatabaseEraser($databaseQueryBuilderProvider);
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function isMarkedAsSingleton()
    {
        $this->assertTrue($this->dbEraser->getClass()->hasAnnotation('Singleton'));
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->dbEraser->getClass()
                                         ->getConstructor()
                                         ->hasAnnotation('Inject')
        );
    }

    /**
     * test that trying to delete a class that does not have an entity annotation throws an exception
     *
     * @test
     * @expectedException  stubPersistenceException
     */
    public function byPrimaryKeysNonEntityClass()
    {
        $this->dbEraser->deleteByPrimaryKeys($this->mockConnection, new MockNoEntityAnnotationEntity());
    }

    /**
     * check that a class that is missing any primary keys throws an exception
     *
     * @test
     * @expectedException  stubDatabaseEraserException
     */
    public function byPrimaryKeysClassWithoutIdAnnotation()
    {
        $this->dbEraser->deleteByPrimaryKeys($this->mockConnection, new MockNoPrimaryKeyEntity());
    }

    /**
     * test that deleting data of an object with its primary keys
     *
     * @test
     */
    public function byPrimaryKeys()
    {
        $entity = new MockNoTableAnnotationEntity();
        $entity->setId('mock');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($this->getMock('stubDatabaseResult')));
        $this->dbEraser->deleteByPrimaryKeys($this->mockConnection, $entity);
        $this->assertEquals('MockNoTableAnnotationEntitys', $this->mockQueryBuilder->getDeleteTable());
        $this->assertEquals("(`MockNoTableAnnotationEntitys`.`id` = 'mock')", $this->mockQueryBuilder->getDeleteCriterion()->toSQL());
    }

    /**
     * test that deleting data of an object with its primary keys
     *
     * @test
     */
    public function byPrimaryKeysWithDBTableAnnotation()
    {
        $entity = new MockSinglePrimaryKeyEntity();
        $entity->setId('mock');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($this->getMock('stubDatabaseResult')));
        $this->dbEraser->deleteByPrimaryKeys($this->mockConnection, $entity);
        $this->assertEquals('foo', $this->mockQueryBuilder->getDeleteTable());
        $this->assertEquals("(`foo`.`id` = 'mock')", $this->mockQueryBuilder->getDeleteCriterion()->toSQL());
    }

    /**
     * test that deleting data of an object with a criterion works as expected
     *
     * @test
     */
    public function byCriterion()
    {
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->any())->method('toSQL')->will($this->returnValue('example'));
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->exactly(2))->method('count')->will($this->onConsecutiveCalls(0, 1));
        $data = $this->dbEraser->deleteByCriterion($this->mockConnection, $mockCriterion, new stubReflectionClass('MockNoTableAnnotationEntity'));
        $this->assertEquals(0, $data);
        $data = $this->dbEraser->deleteByCriterion($this->mockConnection, $mockCriterion, new stubReflectionClass('MockNoTableAnnotationEntity'));
        $this->assertEquals(1, $data);
        $this->assertEquals('MockNoTableAnnotationEntitys', $this->mockQueryBuilder->getDeleteTable());
        $this->assertEquals('example', $this->mockQueryBuilder->getDeleteCriterion()->toSQL());
    }

    /**
     * test that deleting data of an object with a criterion works as expected
     *
     * @test
     */
    public function byCriterionWithDBTableAnnotation()
    {
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->any())->method('toSQL')->will($this->returnValue('example'));
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->exactly(2))->method('count')->will($this->onConsecutiveCalls(0, 1));
        $data = $this->dbEraser->deleteByCriterion($this->mockConnection, $mockCriterion, new stubReflectionClass('MockSinglePrimaryKeyEntity'));
        $this->assertEquals(0, $data);
        $data = $this->dbEraser->deleteByCriterion($this->mockConnection, $mockCriterion, new stubReflectionClass('MockSinglePrimaryKeyEntity'));
        $this->assertEquals(1, $data);
        $this->assertEquals('foo', $this->mockQueryBuilder->getDeleteTable());
        $this->assertEquals('example', $this->mockQueryBuilder->getDeleteCriterion()->toSQL());
    }

    /**
     * test that trying to delete a class that does not have an entity annotation throws an exception
     *
     * @test
     * @expectedException  stubPersistenceException
     */
    public function byCriterionNonEntityClass()
    {
        $this->dbEraser->deleteByCriterion($this->mockConnection, $this->getMock('stubCriterion'), new stubReflectionClass('MockNoEntityAnnotationEntity'));
    }
}
?>