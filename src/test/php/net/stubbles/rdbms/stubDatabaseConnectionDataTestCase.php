<?php
/**
 * Test for net::stubbles::rdbms::stubDatabaseConnectionData.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @version     $Id: stubDatabaseConnectionDataTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseConnectionData');
/**
 * Test for net::stubbles::rdbms::stubDatabaseConnectionData.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @group       rdbms
 */
class stubDatabaseConnectionDataTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function defaultValues()
    {
        $connectionData = new stubDatabaseConnectionData();
        $this->assertEquals(stubDatabaseConnectionData::DEFAULT_ID, $connectionData->getId());
        $this->assertEquals('net::stubbles::rdbms::pdo::stubDatabasePDOConnection', $connectionData->getConnectionClassName());
        $this->assertEquals('', $connectionData->getDSN());
        $this->assertEquals('', $connectionData->getUserName());
        $this->assertEquals('', $connectionData->getPassword());
        $this->assertEquals(array(), $connectionData->getDriverOptions());
        $this->assertFalse($connectionData->hasInitialQuery());
        $this->assertNull($connectionData->getInitialQuery());
    }

    /**
     * @test
     */
    public function setGet()
    {
        $connectionData = new stubDatabaseConnectionData();
        $connectionData->setId('foo');
        $connectionData->setConnectionClassName('bar');
        $connectionData->setDSN('baz');
        $connectionData->setUserName('dummy');
        $connectionData->setPassword('example');
        $connectionData->setDriverOptions(array('key' => 'value'));
        $connectionData->setInitialQuery('set names utf8');
        $this->assertEquals('foo', $connectionData->getId());
        $this->assertEquals('bar', $connectionData->getConnectionClassName());
        $this->assertEquals('baz', $connectionData->getDSN());
        $this->assertEquals('dummy', $connectionData->getUserName());
        $this->assertEquals('example', $connectionData->getPassword());
        $this->assertEquals(array('key' => 'value'), $connectionData->getDriverOptions());
        $this->assertTrue($connectionData->hasInitialQuery());
        $this->assertEquals('set names utf8', $connectionData->getInitialQuery());
    }

    /**
     * @test
     */
    public function equalsComparesInstancesBasedOnTheirId()
    {
        $connectionData1 = new stubDatabaseConnectionData();
        $connectionData1->setId('foo');
        
        $connectionData2 = new stubDatabaseConnectionData();
        $connectionData2->setId('foo');
        
        $connectionData3 = new stubDatabaseConnectionData();
        $connectionData3->setId('bar');
        
        $this->assertTrue($connectionData1->equals($connectionData2));
        $this->assertTrue($connectionData2->equals($connectionData1));
        
        $this->assertFalse($connectionData1->equals($connectionData3));
        $this->assertFalse($connectionData3->equals($connectionData1));
        
        $this->assertFalse($connectionData1->equals('baz'));
    }

    /**
     * @test
     * @expectedException  stubConfigurationException
     * @since              1.1.0
     */
    public function createFromArrayWithMissingDsnPropertyThrowsConfigurationException()
    {
        $connectionData = stubDatabaseConnectionData::fromArray(array());
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function createFromArrayMinimalProperties()
    {
        $connectionData = stubDatabaseConnectionData::fromArray(array('dsn' => 'test'));
        $this->assertInstanceOf('stubDatabaseConnectionData', $connectionData);
        $this->assertEquals(stubDatabaseConnectionData::DEFAULT_ID, $connectionData->getId());
        $this->assertEquals('net::stubbles::rdbms::pdo::stubDatabasePDOConnection', $connectionData->getConnectionClassName());
        $this->assertEquals('test', $connectionData->getDSN());
        $this->assertEquals('', $connectionData->getUserName());
        $this->assertEquals('', $connectionData->getPassword());
        $this->assertEquals(array(), $connectionData->getDriverOptions());
        $this->assertFalse($connectionData->hasInitialQuery());
        $this->assertNull($connectionData->getInitialQuery());
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function createFromArrayFullProperties()
    {
        $connectionData = stubDatabaseConnectionData::fromArray(array('dsn'                 => 'test',
                                                                      'username'            => 'root',
                                                                      'password'            => 'secret',
                                                                      'initialQuery'        => 'SET names UTF-8',
                                                                      'connectionClassName' => 'my::ConnectionClass'
                                                                ),
                                                                'foo'
                          );
        $this->assertInstanceOf('stubDatabaseConnectionData', $connectionData);
        $this->assertEquals('foo', $connectionData->getId());
        $this->assertEquals('my::ConnectionClass', $connectionData->getConnectionClassName());
        $this->assertEquals('test', $connectionData->getDSN());
        $this->assertEquals('root', $connectionData->getUserName());
        $this->assertEquals('secret', $connectionData->getPassword());
        $this->assertEquals(array(), $connectionData->getDriverOptions());
        $this->assertTrue($connectionData->hasInitialQuery());
        $this->assertEquals('SET names UTF-8', $connectionData->getInitialQuery());
    }
}
?>