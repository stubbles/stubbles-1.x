<?php
/**
 * Test for net::stubbles::rdbms::stubPropertyBasedDatabaseInitializer.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @version     $Id: stubPropertyBasedDatabaseInitializerTestCase.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubPropertyBasedDatabaseInitializer',
                      'net::stubbles::reflection::stubReflectionClass'
);
/**
 * Test for net::stubbles::rdbms::stubPropertyBasedDatabaseInitializer.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @since       1.1.0
 */
class stubPropertyBasedDatabaseInitializerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPropertyBasedDatabaseInitializer
     */
    protected $propertyBasesDatabaseInitializer;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->propertyBasesDatabaseInitializer = new stubPropertyBasedDatabaseInitializer(TEST_SRC_PATH . '/resources');
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $class       = $this->propertyBasesDatabaseInitializer->getClass();
        $constructor = $class->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $this->assertTrue($constructor->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.config.path',
                            $constructor->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetDescriptorMethod()
    {
        $setDescriptorMethod = $this->propertyBasesDatabaseInitializer->getClass()
                                                                      ->getMethod('setDescriptor');
        $this->assertTrue($setDescriptorMethod->hasAnnotation('Inject'));
        $this->assertTrue($setDescriptorMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setDescriptorMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.rdbms.descriptor',
                            $setDescriptorMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function isDefaultImplementationForDatabaseInitializerInterface()
    {
        $refClass = new stubReflectionClass('net::stubbles::rdbms::stubDatabaseInitializer');
        $this->assertEquals($this->propertyBasesDatabaseInitializer->getClassName(),
                            $refClass->getAnnotation('ImplementedBy')
                                     ->getDefaultImplementation()
                                     ->getFullQualifiedClassName()
        );
    }

    /**
     * @test
     */
    public function defaultDescriptor()
    {
        $this->assertFalse($this->propertyBasesDatabaseInitializer->hasConnectionData('foo'));
        $this->assertTrue($this->propertyBasesDatabaseInitializer->hasConnectionData('default'));
        $connectionData = $this->propertyBasesDatabaseInitializer->getConnectionData('default');
        $this->assertInstanceOf('stubDatabaseConnectionData', $connectionData);
        $this->assertEquals('default', $connectionData->getId());
        $this->assertEquals('net::stubbles::rdbms::pdo::stubDatabasePDOConnection', $connectionData->getConnectionClassName());
        $this->assertEquals('mysql:host=localhost;dbname=example', $connectionData->getDSN());
        $this->assertEquals('', $connectionData->getUserName());
        $this->assertEquals('', $connectionData->getPassword());
        $this->assertEquals(array(), $connectionData->getDriverOptions());
        $this->assertFalse($connectionData->hasInitialQuery());
        $this->assertNull($connectionData->getInitialQuery());
    }

    /**
     * @test
     */
    public function setDescriptorReturnsItself()
    {
        $this->assertSame($this->propertyBasesDatabaseInitializer,
                          $this->propertyBasesDatabaseInitializer->setDescriptor('rdbms-foo')
        );

        $this->assertTrue($this->propertyBasesDatabaseInitializer->hasConnectionData('foo'));
        $this->assertFalse($this->propertyBasesDatabaseInitializer->hasConnectionData('default'));
        $connectionData = $this->propertyBasesDatabaseInitializer->getConnectionData('foo');
        $this->assertInstanceOf('stubDatabaseConnectionData', $connectionData);
        $this->assertEquals('foo', $connectionData->getId());
        $this->assertEquals('my::ConnectionClass', $connectionData->getConnectionClassName());
        $this->assertEquals('mysql:host=localhost;dbname=foo', $connectionData->getDSN());
        $this->assertEquals('root', $connectionData->getUserName());
        $this->assertEquals('secret', $connectionData->getPassword());
        $this->assertEquals(array(), $connectionData->getDriverOptions());
        $this->assertTrue($connectionData->hasInitialQuery());
        $this->assertEquals('set names utf8', $connectionData->getInitialQuery());
    }

    /**
     * @test
     * @expectedException  stubConfigurationException
     */
    public function getNonExistingConnectionDataThrowsConfigurationException()
    {
        $this->propertyBasesDatabaseInitializer->getConnectionData('foo');
    }
}
?>