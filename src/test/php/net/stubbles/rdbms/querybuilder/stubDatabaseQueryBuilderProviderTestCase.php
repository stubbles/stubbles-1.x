<?php
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseQueryBuilderProvider.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @version     $Id: stubDatabaseQueryBuilderProviderTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseQueryBuilderProvider');
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseQueryBuilderProvider.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @since       1.7.0
 * @group       rdbms
 * @group       rdbms_querybuilder
 * @group       issue_270
 */
class stubDatabaseQueryBuilderProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseQueryBuilderProvider
     */
    protected $databaseQueryBuilderProvider;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInjector                 = $this->getMock('stubInjector');
        $this->databaseQueryBuilderProvider = new stubDatabaseQueryBuilderProvider($this->mockInjector);
    }

    /**
     * @test
     */
    public function isMarkedAsSingleton()
    {
        $this->assertTrue($this->databaseQueryBuilderProvider->getClass()
                                                             ->hasAnnotation('Singleton')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->databaseQueryBuilderProvider->getClass()
                                                             ->getConstructor()
                                                             ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetAvailableQueryBuildersMethod()
    {
        $method = $this->databaseQueryBuilderProvider->getClass()
                                                     ->getMethod('setAvailableQueryBuilders');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.rdbms.querybuilders', $method->getAnnotation('Named')->getName());
    }

    /**
     * @test
     * @expectedException  stubDatabaseQueryBuilderException
     */
    public function getThrowsDatabaseQueryBuilderExceptionIfQueryBuilderIsNotAvailable()
    {
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->databaseQueryBuilderProvider->get('notAvailable');
    }

    /**
     * @test
     */
    public function getReturnsAvailableQueryBuilderProvider()
    {
        $mockQueryBuilder = $this->getMock('stubDatabaseQueryBuilder');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->will($this->returnValue($mockQueryBuilder));
        $this->assertSame($mockQueryBuilder,
                          $this->databaseQueryBuilderProvider->get('mysql')
        );
    }

    /**
     * @test
     */
    public function getWithDifferentQueryBuilders()
    {
        $mockQueryBuilder = $this->getMock('stubDatabaseQueryBuilder');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('foo::QueryBuilder'))
                           ->will($this->returnValue($mockQueryBuilder));
        $this->assertSame($mockQueryBuilder,
                          $this->databaseQueryBuilderProvider->setAvailableQueryBuilders(array('foo' => 'foo::QueryBuilder'))
                                                             ->get('foo')
        );
    }

    /**
     * @test
     * @expectedException  stubDatabaseQueryBuilderException
     */
    public function createThrowsDatabaseQueryBuilderExceptionIfQueryBuilderIsNotAvailable()
    {
        $mockDatabaseConnection = $this->getMock('stubDatabaseConnection');
        $mockDatabaseConnection->expects($this->once())
                               ->method('getDatabase')
                               ->will($this->returnValue('notAvailable'));
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->databaseQueryBuilderProvider->create($mockDatabaseConnection);
    }

    /**
     * @test
     */
    public function createReturnsAvailableQueryBuilderProvider()
    {
        $mockDatabaseConnection = $this->getMock('stubDatabaseConnection');
        $mockDatabaseConnection->expects($this->once())
                               ->method('getDatabase')
                               ->will($this->returnValue('MySQL'));
        $mockQueryBuilder = $this->getMock('stubDatabaseQueryBuilder');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->will($this->returnValue($mockQueryBuilder));
        $this->assertSame($mockQueryBuilder,
                          $this->databaseQueryBuilderProvider->create($mockDatabaseConnection)
        );
    }

    /**
     * @test
     */
    public function createWithDifferentQueryBuilders()
    {
        $mockDatabaseConnection = $this->getMock('stubDatabaseConnection');
        $mockDatabaseConnection->expects($this->once())
                               ->method('getDatabase')
                               ->will($this->returnValue('foo'));
        $mockQueryBuilder = $this->getMock('stubDatabaseQueryBuilder');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('foo::QueryBuilder'))
                           ->will($this->returnValue($mockQueryBuilder));
        $this->assertSame($mockQueryBuilder,
                          $this->databaseQueryBuilderProvider->setAvailableQueryBuilders(array('foo' => 'foo::QueryBuilder'))
                                                             ->create($mockDatabaseConnection)
        );
    }
}
?>