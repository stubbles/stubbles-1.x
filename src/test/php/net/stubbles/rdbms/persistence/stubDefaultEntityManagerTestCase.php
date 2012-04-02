<?php
/**
 * Test for net::stubbles::rdbms::persistence::stubDefaultEntityManager.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_test
 * @version     $Id: stubDefaultEntityManagerTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::persistence::stubDefaultEntityManager');
require_once dirname(__FILE__) . '/MockSinglePrimaryKeyEntity.php';
/**
 * Test for net::stubbles::rdbms::persistence::stubDefaultEntityManager.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_test
 * @group       rdbms
 * @group       rdbms_persistence
 */
class stubDefaultEntityManagerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDefaultEntityManager
     */
    protected $defaultEntityManager;
    /**
     * mocked connection
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockConnection;
    /**
     * mocked finder
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockFinder;
    /**
     * mocked serializer
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSerializer;
    /**
     * mocked eraser
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockEraser;
    /**
     * instance used throughout test cases for passing as argument
     *
     * @var  stubReflectionClass
     */
    protected $entityRefClass;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockConnection       = $this->getMock('stubDatabaseConnection');
        $this->mockFinder           = $this->getMock('stubDatabaseFinder',
                                                     array(),
                                                     array(),
                                                     '',
                                                     false
                                      );
        $this->mockSerializer       = $this->getMock('stubDatabaseSerializer',
                                                     array(),
                                                     array(),
                                                     '',
                                                     false
                                      );
        $this->mockEraser           = $this->getMock('stubDatabaseEraser',
                                                     array(),
                                                     array(),
                                                     '',
                                                     false
                                      );
        $this->defaultEntityManager = new stubDefaultEntityManager($this->mockConnection,
                                                                   $this->mockFinder,
                                                                   $this->mockSerializer,
                                                                   $this->mockEraser
                                      );
        $this->entityRefClass       = new stubReflectionClass('MockSinglePrimaryKeyEntity');
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function isDefaultImplementationForEntityManager()
    {
        $refClass = new stubReflectionClass('net::stubbles::rdbms::persistence::stubEntityManager');
        $this->assertTrue($refClass->hasAnnotation('ImplementedBy'));

        $this->assertEquals($this->defaultEntityManager->getClassName(),
                            $refClass->getAnnotation('ImplementedBy')
                                     ->getDefaultImplementation()
                                     ->getFullQualifiedClassName()
        );
    }

    /**
     * @since  1.7.0
     * @test
     * @group  issue_270
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->defaultEntityManager->getClass()
                                                     ->getConstructor()
                                                     ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function findByPrimaryKeyFacadesFinder()
    {
        $return = new stdClass(); // faked return value, only ensure return from finder is returned
        $this->mockFinder->expects($this->once())
                         ->method('findByPrimaryKeys')
                         ->with($this->equalTo($this->mockConnection),
                                $this->equalTo($this->entityRefClass),
                                $this->equalTo(array('foo_id' => 303))
                           )
                         ->will($this->returnValue($return));
        $this->assertSame($return,
                          $this->defaultEntityManager->findByPrimaryKey($this->entityRefClass,
                                                                        array('foo_id' => 303)
                                                       )
        );
    }

    /**
     * @test
     */
    public function findByCriterionFacadesFinderWithoutOptionalParameters()
    {
        $return        = new stdClass(); // faked return value, only ensure return from finder is returned
        $mockCriterion = $this->getMock('stubCriterion');
        $this->mockFinder->expects($this->once())
                         ->method('findByCriterion')
                         ->with($this->equalTo($this->mockConnection),
                                $this->equalTo($mockCriterion),
                                $this->equalTo($this->entityRefClass),
                                $this->equalTo(null),
                                $this->equalTo(null),
                                $this->equalTo(null)
                           )
                         ->will($this->returnValue($return));
        $this->assertSame($return,
                          $this->defaultEntityManager->findByCriterion($mockCriterion,
                                                                       $this->entityRefClass
                                                       )
        );
    }

    /**
     * @test
     */
    public function findByCriterionFacadesFinderWithOptionalParameters()
    {
        $return        = new stdClass(); // faked return value, only ensure return from finder is returned
        $mockCriterion = $this->getMock('stubCriterion');
        $this->mockFinder->expects($this->once())
                         ->method('findByCriterion')
                         ->with($this->equalTo($this->mockConnection),
                                $this->equalTo($mockCriterion),
                                $this->equalTo($this->entityRefClass),
                                $this->equalTo('foo'),
                                $this->equalTo(313),
                                $this->equalTo(20)
                           )
                         ->will($this->returnValue($return));
        $this->assertSame($return,
                          $this->defaultEntityManager->findByCriterion($mockCriterion,
                                                                       $this->entityRefClass,
                                                                       'foo',
                                                                       313,
                                                                       20
                                                       )
        );
    }

    /**
     * @test
     */
    public function findAllFacadesFinderWithoutOptionalParameters()
    {
        $return = new stdClass(); // faked return value, only ensure return from finder is returned
        $this->mockFinder->expects($this->once())
                         ->method('findAll')
                         ->with($this->equalTo($this->mockConnection),
                                $this->equalTo($this->entityRefClass),
                                $this->equalTo(null),
                                $this->equalTo(null),
                                $this->equalTo(null)
                           )
                         ->will($this->returnValue($return));
        $this->assertSame($return,
                          $this->defaultEntityManager->findAll($this->entityRefClass)
        );
    }

    /**
     * @test
     */
    public function findAllFacadesFinderWithOptionalParameters()
    {
        $return = new stdClass(); // faked return value, only ensure return from finder is returned
        $this->mockFinder->expects($this->once())
                         ->method('findAll')
                         ->with($this->equalTo($this->mockConnection),
                                $this->equalTo($this->entityRefClass),
                                $this->equalTo('foo'),
                                $this->equalTo(313),
                                $this->equalTo(20)
                           )
                         ->will($this->returnValue($return));
        $this->assertSame($return,
                          $this->defaultEntityManager->findAll($this->entityRefClass,
                                                               'foo',
                                                               313,
                                                               20
                                                       )
        );
    }

    /**
     * @test
     */
    public function insertFacadesSerializer()
    {
        $entity = $this->getMock('stubObject');
        $this->mockSerializer->expects($this->once())
                             ->method('insert')
                             ->with($this->equalTo($this->mockConnection),
                                    $this->equalTo($entity)
                               )
                             ->will($this->returnValue(stubDatabaseSerializer::INSERT));
        $this->assertEquals(stubDatabaseSerializer::INSERT,
                            $this->defaultEntityManager->insert($entity)
        );
    }

    /**
     * @test
     */
    public function updateFacadesSerializer()
    {
        $entity = $this->getMock('stubObject');
        $this->mockSerializer->expects($this->once())
                             ->method('update')
                             ->with($this->equalTo($this->mockConnection),
                                    $this->equalTo($entity)
                               )
                             ->will($this->returnValue(stubDatabaseSerializer::UPDATE));
        $this->assertEquals(stubDatabaseSerializer::UPDATE,
                            $this->defaultEntityManager->update($entity)
        );
    }

    /**
     * @test
     */
    public function serializeFacadesSerializer()
    {
        $entity = $this->getMock('stubObject');
        $this->mockSerializer->expects($this->once())
                             ->method('serialize')
                             ->with($this->equalTo($this->mockConnection),
                                    $this->equalTo($entity)
                               )
                             ->will($this->returnValue(stubDatabaseSerializer::UPDATE));
        $this->assertEquals(stubDatabaseSerializer::UPDATE,
                            $this->defaultEntityManager->serialize($entity)
        );
    }

    /**
     * @test
     */
    public function deleteByPrimaryKeysFacadesEraser()
    {
        $entity = $this->getMock('stubObject');
        $this->mockEraser->expects($this->once())
                         ->method('deleteByPrimaryKeys')
                         ->with($this->equalTo($this->mockConnection),
                                $this->equalTo($entity)
                           );
        $this->defaultEntityManager->deleteByPrimaryKeys($entity);
    }

    /**
     * @test
     */
    public function deleteByCriterionFacadesEraser()
    {
        $mockCriterion = $this->getMock('stubCriterion');
        $this->mockEraser->expects($this->once())
                         ->method('deleteByCriterion')
                         ->with($this->equalTo($this->mockConnection),
                                $this->equalTo($mockCriterion),
                                $this->equalTo($this->entityRefClass)
                           )
                         ->will($this->returnValue(4));
        $this->assertEquals(4,
                            $this->defaultEntityManager->deleteByCriterion($mockCriterion,
                                                                           $this->entityRefClass
                                                         )
        );
    }
}
?>