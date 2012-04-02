<?php
/**
 * Test for net::stubbles::rdbms::persistence::finder::stubDatabaseFinder.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_finder_test
 * @version     $Id: stubDatabaseFinderTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::persistence::finder::stubDatabaseFinder',
                      'net::stubbles::rdbms::stubDatabaseResult'
);
require_once dirname(__FILE__) . '/../../querybuilder/TeststubDatabaseQueryBuilder.php';
require_once dirname(__FILE__) . '/../MockNoTableAnnotationEntity.php';
require_once dirname(__FILE__) . '/../MockSinglePrimaryKeyEntity.php';
require_once dirname(__FILE__) . '/../MockNoEntityAnnotationEntity.php';
/**
 * Test for net::stubbles::rdbms::persistence::finder::stubDatabaseFinder.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_finder_test
 * @group       rdbms
 * @group       rdbms_persistence
 */
class stubDatabaseFinderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseFinder
     */
    protected $dbFinder;
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
        $this->dbFinder = new stubDatabaseFinder($databaseQueryBuilderProvider);
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function isMarkedAsSingleton()
    {
        $this->assertTrue($this->dbFinder->getClass()->hasAnnotation('Singleton'));
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->dbFinder->getClass()
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
    public function byPrimaryKeysNonEntity()
    {
        $this->dbFinder->findByPrimaryKeys($this->mockConnection, new stubReflectionClass('MockNoEntityAnnotationEntity'), array());
    }

    /**
     * test that finding data of an object with its primary keys
     *
     * @test
     */
    public function byPrimaryKeys()
    {
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->exactly(2))
                   ->method('fetch')
                   ->will($this->onConsecutiveCalls(false, array('id' => 'mock', 'bar' => 'Here is bar.', 'default' => 'And this is default.')));
        $this->assertNull($this->dbFinder->findByPrimaryKeys($this->mockConnection, new stubReflectionClass('MockSinglePrimaryKeyEntity'), array('id' => 'mock')));
        $singlePrimaryKey = $this->dbFinder->findByPrimaryKeys($this->mockConnection, new stubReflectionClass('MockSinglePrimaryKeyEntity'), array('id' => 'mock'));
        $this->assertEquals('mock', $singlePrimaryKey->getId());
        $this->assertEquals('Here is bar.', $singlePrimaryKey->withAnnotation());
        $this->assertEquals('And this is default.', $singlePrimaryKey->withDefaultValue());
        $this->assertEquals('foo', $this->mockQueryBuilder->getSelect()->getBaseTableName());
    }

    /**
     * test that finding data of an object with its primary keys
     *
     * @test
     */
    public function byPrimaryKeysWithoutTableAnnotation()
    {
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->exactly(2))
                   ->method('fetch')
                   ->will($this->onConsecutiveCalls(false, array('id' => 'mock', 'bar' => 'Here is bar.', 'defaultValue' => 'And this is default.')));
        $this->assertNull($this->dbFinder->findByPrimaryKeys($this->mockConnection, new stubReflectionClass('MockNoTableAnnotationEntity'), array('id' => 'mock')));
        $entity = $this->dbFinder->findByPrimaryKeys($this->mockConnection, new stubReflectionClass('MockNoTableAnnotationEntity'), array('id' => 'mock'));
        $this->assertEquals('mock', $entity->getId());
        $this->assertEquals('Here is bar.', $entity->withAnnotation());
        $this->assertEquals('And this is default.', $entity->getDefaultValue());
        $this->assertEquals('MockNoTableAnnotationEntitys', $this->mockQueryBuilder->getSelect()->getBaseTableName());
    }

    /**
     * test that trying to find a class that does not have an entity annotation throws an exception
     *
     * @test
     * @expectedException  stubPersistenceException
     */
    public function byCriterionNonEntity()
    {
        $this->dbFinder->findByCriterion($this->mockConnection, $this->getMock('stubCriterion'), new stubReflectionClass('MockNoEntityAnnotationEntity'));
    }

    /**
     * test that finding data of an object with a criterion works as expected
     *
     * @test
     */
    public function byCriterion()
    {
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->any())->method('toSQL')->will($this->returnValue('example'));
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->exactly(2))
                   ->method('fetchAll')
                   ->will($this->onConsecutiveCalls(false, array(array('bar' => 'Here is bar.', 'default' => 'And this is default.'))));
        $finderResult = $this->dbFinder->findByCriterion($this->mockConnection, $mockCriterion, new stubReflectionClass('MockSinglePrimaryKeyEntity'));
        $this->assertEquals(0, $finderResult->count());
        $finderResult = $this->dbFinder->findByCriterion($this->mockConnection, $mockCriterion, new stubReflectionClass('MockSinglePrimaryKeyEntity'));
        $this->assertEquals(1, $finderResult->count());
        $data = $finderResult->current();
        $this->assertEquals('Here is bar.', $data->withAnnotation());
        $this->assertEquals('And this is default.', $data->withDefaultValue());
        $select = $this->mockQueryBuilder->getSelect();
        $this->assertEquals('foo', $select->getBaseTableName());
        $this->assertEquals('bar ASC', $select->getOrderedBy());
        $this->assertTrue($select->hasCriterion());
    }

    /**
     * test that finding data of an object with a criterion works as expected
     *
     * @test
     */
    public function byCriterionOverruleOrderBy()
    {
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->any())->method('toSQL')->will($this->returnValue('example'));
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->once())
                   ->method('fetchAll')
                   ->will($this->returnValue(array(array('bar' => 'Here is bar.', 'default' => 'And this is default.'))));
        $finderResult = $this->dbFinder->findByCriterion($this->mockConnection, $mockCriterion, new stubReflectionClass('MockSinglePrimaryKeyEntity'), 'blub DESC');
        $this->assertEquals(1, $finderResult->count());
        $data = $finderResult->current();
        $this->assertEquals('Here is bar.', $data->withAnnotation());
        $this->assertEquals('And this is default.', $data->withDefaultValue());
        $select = $this->mockQueryBuilder->getSelect();
        $this->assertEquals('foo', $select->getBaseTableName());
        $this->assertEquals('blub DESC', $select->getOrderedBy());
        $this->assertFalse($select->hasLimit());
        $this->assertNull($select->getOffset());
        $this->assertNull($select->getAmount());
        $this->assertTrue($select->hasCriterion());
    }

    /**
     * test that finding data of an object with a criterion works as expected
     *
     * @test
     */
    public function byCriterionOverruleLimitClause()
    {
        $mockCriterion = $this->getMock('stubCriterion');
        $mockCriterion->expects($this->any())->method('toSQL')->will($this->returnValue('example'));
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->once())
                   ->method('fetchAll')
                   ->will($this->returnValue(array(array('bar' => 'Here is bar.', 'default' => 'And this is default.'))));
        $finderResult = $this->dbFinder->findByCriterion($this->mockConnection, $mockCriterion, new stubReflectionClass('MockSinglePrimaryKeyEntity'), null, 50, 10);
        $this->assertEquals(1, $finderResult->count());
        $data = $finderResult->current();
        $this->assertEquals('Here is bar.', $data->withAnnotation());
        $this->assertEquals('And this is default.', $data->withDefaultValue());
        $select = $this->mockQueryBuilder->getSelect();
        $this->assertEquals('foo', $select->getBaseTableName());
        $this->assertEquals('bar ASC', $select->getOrderedBy());
        $this->assertTrue($select->hasLimit());
        $this->assertEquals(50, $select->getOffset());
        $this->assertEquals(10, $select->getAmount());
        $this->assertTrue($select->hasCriterion());
    }

    /**
     * test that finding data for all instances of an object works as expected
     *
     * @test
     */
    public function findAll()
    {
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->exactly(2))
                   ->method('fetchAll')
                   ->will($this->onConsecutiveCalls(false, array(array('bar' => 'Here is bar.', 'default' => 'And this is default.'))));
        $finderResult = $this->dbFinder->findAll($this->mockConnection, new stubReflectionClass('MockSinglePrimaryKeyEntity'));
        $this->assertEquals(0, $finderResult->count());
        $finderResult = $this->dbFinder->findAll($this->mockConnection, new stubReflectionClass('MockSinglePrimaryKeyEntity'));
        $this->assertEquals(1, $finderResult->count());
        $data = $finderResult->current();
        $this->assertEquals('Here is bar.', $data->withAnnotation());
        $this->assertEquals('And this is default.', $data->withDefaultValue());
        $select = $this->mockQueryBuilder->getSelect();
        $this->assertEquals('foo', $select->getBaseTableName());
        $this->assertEquals('bar ASC', $select->getOrderedBy());
        $this->assertFalse($select->hasLimit());
        $this->assertNull($select->getOffset());
        $this->assertNull($select->getAmount());
        $this->assertFalse($select->hasCriterion());
    }

    /**
     * test that finding data for all instances of an object works as expected
     *
     * @test
     */
    public function findAllOverruleOrderBy()
    {
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->once())
                   ->method('fetchAll')
                   ->will($this->returnValue(array(array('bar' => 'Here is bar.', 'default' => 'And this is default.'))));
        $finderResult = $this->dbFinder->findAll($this->mockConnection, new stubReflectionClass('MockSinglePrimaryKeyEntity'), 'blub DESC');
        $this->assertEquals(1, $finderResult->count());
        $data = $finderResult->current();
        $this->assertEquals('Here is bar.', $data->withAnnotation());
        $this->assertEquals('And this is default.', $data->withDefaultValue());
        $select = $this->mockQueryBuilder->getSelect();
        $this->assertEquals('foo', $select->getBaseTableName());
        $this->assertEquals('blub DESC', $select->getOrderedBy());
        $this->assertFalse($select->hasLimit());
        $this->assertNull($select->getOffset());
        $this->assertNull($select->getAmount());
        $this->assertFalse($select->hasCriterion());
    }

    /**
     * test that finding data for all instances of an object works as expected
     *
     * @test
     */
    public function findAllOverruleLimitClause()
    {
        $mockResult = $this->getMock('stubDatabaseResult');
        $this->mockConnection->expects($this->any())->method('query')->will($this->returnValue($mockResult));
        $mockResult->expects($this->once())
                   ->method('fetchAll')
                   ->will($this->returnValue(array(array('bar' => 'Here is bar.', 'default' => 'And this is default.'))));
        $finderResult = $this->dbFinder->findAll($this->mockConnection, new stubReflectionClass('MockSinglePrimaryKeyEntity'), null, 50, 10);
        $this->assertEquals(1, $finderResult->count());
        $data = $finderResult->current();
        $this->assertEquals('Here is bar.', $data->withAnnotation());
        $this->assertEquals('And this is default.', $data->withDefaultValue());
        $select = $this->mockQueryBuilder->getSelect();
        $this->assertEquals('foo', $select->getBaseTableName());
        $this->assertEquals('bar ASC', $select->getOrderedBy());
        $this->assertTrue($select->hasLimit());
        $this->assertEquals(50, $select->getOffset());
        $this->assertEquals(10, $select->getAmount());
        $this->assertFalse($select->hasCriterion());
    }
}
?>