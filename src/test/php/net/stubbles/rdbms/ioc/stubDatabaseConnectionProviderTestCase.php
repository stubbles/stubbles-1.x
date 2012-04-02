<?php
/**
 * Test for net::stubbles::rdbms::ioc::stubDatabaseConnectionProvider.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc_test
 * @version     $Id: stubDatabaseConnectionProviderTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::ioc::stubDatabaseConnectionProvider',
                      'net::stubbles::reflection::stubReflectionClass'
);
require_once dirname(__FILE__) . '/../stubDatabaseDummyConnection.php';
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc_test
 */
class stubDatabaseDummyConnection2 extends stubDatabaseDummyConnection { }
/**
 * Test for net::stubbles::rdbms::ioc::stubDatabaseConnectionProvider.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc_test
 * @group       rdbms
 * @group       rdbms_ioc
 */
class stubDatabaseConnectionProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseConnectionProvider
     */
    protected $databaseConnectionProvider;
    /**
     * mocked database initializer
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockDatabaseInitializer;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockDatabaseInitializer    = $this->getMock('stubDatabaseInitializer');
        $this->databaseConnectionProvider = new stubDatabaseConnectionProvider($this->mockDatabaseInitializer);
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationsPresent()
    {
        $class       = $this->databaseConnectionProvider->getClass();
        $constructor = $class->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $setFallbackMethod = $class->getMethod('setFallback');
        $this->assertTrue($setFallbackMethod->hasAnnotation('Inject'));
        $this->assertTrue($setFallbackMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setFallbackMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.rdbms.fallback',
                            $setFallbackMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function isProviderForDatabaseConnection()
    {
        $refClass = new stubReflectionClass('net::stubbles::rdbms::stubDatabaseConnection');
        $this->assertTrue($refClass->hasAnnotation('ProvidedBy'));
        $this->assertEquals($this->databaseConnectionProvider->getClassName(),
                            $refClass->getAnnotation('ProvidedBy')
                                     ->getProviderClass()
                                     ->getFullQualifiedClassName()
        );
    }

    /**
     * @test
     * @expectedException  stubDatabaseException
     */
    public function namedConnectionDoesNotExistFallDisabled()
    {
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo('namedFoo'))
                                      ->will($this->returnValue(false));
        $this->databaseConnectionProvider->setFallback(false)
                                         ->get('namedFoo');
    }

    /**
     * named connection does not exist and fallback is enabled > default connection
     *
     * @test
     */
    public function namedConnectionDoesNotExistFallEnabled()
    {
        $connectionData = new stubDatabaseConnectionData();
        $connectionData->setConnectionClassName('stubDatabaseDummyConnection');
        $this->mockDatabaseInitializer->expects($this->at(0))
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo('namedFoo'))
                                      ->will($this->returnValue(false));
        $this->mockDatabaseInitializer->expects($this->at(1))
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo(stubDatabaseConnectionData::DEFAULT_ID))
                                      ->will($this->returnValue(true));
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('getConnectionData')
                                      ->with($this->equalTo(stubDatabaseConnectionData::DEFAULT_ID))
                                      ->will($this->returnValue($connectionData));
        $connection = $this->databaseConnectionProvider->get('namedFoo');
        $this->assertInstanceOf('stubDatabaseDummyConnection', $connection);
    }

    /**
     * named connection does not exist and fallback is enabled > default connection
     *
     * @test
     * @expectedException  stubDatabaseException
     */
    public function namedConnectionDoesNotExistFallEnabledDefaultDoesNotExist()
    {
        $connectionData = new stubDatabaseConnectionData();
        $connectionData->setConnectionClassName('stubDatabaseDummyConnection');
        $this->mockDatabaseInitializer->expects($this->at(0))
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo('namedFoo'))
                                      ->will($this->returnValue(false));
        $this->mockDatabaseInitializer->expects($this->at(1))
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo(stubDatabaseConnectionData::DEFAULT_ID))
                                      ->will($this->returnValue(false));
        $this->mockDatabaseInitializer->expects($this->never())
                                      ->method('getConnectionData');
        $this->databaseConnectionProvider->get('namedFoo');
    }

    /**
     * named connection does exist > default connection
     *
     * @test
     */
    public function namedConnectionDoesExist()
    {
        $connectionData = new stubDatabaseConnectionData();
        $connectionData->setConnectionClassName('stubDatabaseDummyConnection2');
        $connectionData->setId('namedFoo');
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo('namedFoo'))
                                      ->will($this->returnValue(true));
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('getConnectionData')
                                      ->with($this->equalTo('namedFoo'))
                                      ->will($this->returnValue($connectionData));
        $connection1 = $this->databaseConnectionProvider->get('namedFoo');
        $this->assertInstanceOf('stubDatabaseDummyConnection2', $connection1);
        $connection2 = $this->databaseConnectionProvider->get('namedFoo');
        $this->assertInstanceOf('stubDatabaseDummyConnection2', $connection2);
        $this->assertSame($connection1, $connection2);
    }

    /**
     * always return the default connection if no named connection is requested
     *
     * @test
     */
    public function defaultConnection()
    {
        $connectionData = new stubDatabaseConnectionData();
        $connectionData->setConnectionClassName('stubDatabaseDummyConnection');
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo(stubDatabaseConnectionData::DEFAULT_ID))
                                      ->will($this->returnValue(true));
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('getConnectionData')
                                      ->with($this->equalTo(stubDatabaseConnectionData::DEFAULT_ID))
                                      ->will($this->returnValue($connectionData));
        $connection1 = $this->databaseConnectionProvider->get();
        $this->assertInstanceOf('stubDatabaseDummyConnection', $connection1);
        $connection2 = $this->databaseConnectionProvider->get();
        $this->assertInstanceOf('stubDatabaseDummyConnection', $connection2);
        $this->assertSame($connection1, $connection2);
    }

    /**
     * always return the default connection if no named connection is requested
     *
     * @test
     * @expectedException  stubDatabaseException
     */
    public function defaultConnectionDoesNotExist()
    {
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo(stubDatabaseConnectionData::DEFAULT_ID))
                                      ->will($this->returnValue(false));
        $this->mockDatabaseInitializer->expects($this->never())
                                      ->method('getConnectionData');
        $this->databaseConnectionProvider->get();
    }

    /**
     * illegal database connection class throws exception
     *
     * @test
     * @expectedException  stubDatabaseException
     */
    public function illegalDatabaseConnectionClassThrowsException()
    {
        $connectionData = new stubDatabaseConnectionData();
        $connectionData->setConnectionClassName('stdClass');
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('hasConnectionData')
                                      ->with($this->equalTo(stubDatabaseConnectionData::DEFAULT_ID))
                                      ->will($this->returnValue(true));
        $this->mockDatabaseInitializer->expects($this->once())
                                      ->method('getConnectionData')
                                      ->with($this->equalTo(stubDatabaseConnectionData::DEFAULT_ID))
                                      ->will($this->returnValue($connectionData));
        $this->databaseConnectionProvider->get();
    }
}
?>