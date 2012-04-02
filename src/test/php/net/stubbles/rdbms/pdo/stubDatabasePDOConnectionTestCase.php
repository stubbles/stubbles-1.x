<?php
/**
 * Test for net::stubbles::rdbms::pdo::stubDatabasePDOConnection.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @version     $Id: stubDatabasePDOConnectionTestCase.php 2922 2011-01-14 17:59:05Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::pdo::stubDatabasePDOConnection');
if (extension_loaded('pdo') == true) {
    /**
     * Helper class for the test.
     *
     * @package     stubbles
     * @subpackage  rdbms_test
     */
    class TestPDO extends PDO
    {
        public function __construct($dsn, $username = null, $password = null , array $driver_options = array()) {}

        /**
         * provide the method as the reflection api would say it has no parameters otherwise
         *
         * @param  string  $statement  optional
         * @param  int     $fetch      optional
         * @param  mixed   $three      optional
         * @param  array   $ctorargs   optional
         */
        public function query($statement, $fetch = 0, $three = null, array $ctorargs = null) {}
    }

    /**
     * Helper class for the test.
     *
     * @package     stubbles
     * @subpackage  rdbms_test
     */
    class TestPDOStatement extends PDOStatement {}
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 */
class TeststubDatabasePDOConnection extends stubDatabasePDOConnection
{
    protected $mockPDO;
    
    public function setPDO($mockPDO)
    {
        $this->mockPDO = $mockPDO;
    }
    
    protected function createPDO()
    {
        $this->pdo = $this->mockPDO;
    }
}
/**
 * Test for net::stubbles::rdbms::pdo::stubDatabasePDOConnection.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @group       rdbms
 * @group       rdbms_pdo
 */
class stubDatabasePDOConnectionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubDatabasePDOConnection
     */
    protected $pdoConnection;
    /**
     * the connection data instance
     * 
     * @var  stubDatabaseConnectionData
     */
    protected $connectionData;
    /**
     * mock for pdo
     *
     * @var  SimpleMock
     */
    protected $mockPDO;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('pdo') === false) {
            $this->markTestSkipped('net::stubbles::rdbms::pdo::stubDatabasePDOConnection requires PHP-extension "pdo".');
        }
        
        $this->mockPDO        = $this->getMock('TestPDO', array(), array('mockDSN'));
        $this->connectionData = new stubDatabaseConnectionData();
        $this->pdoConnection  = new TeststubDatabasePDOConnection($this->connectionData);
        $this->pdoConnection->setPDO($this->mockPDO);
    }

    /**
     * clear test environment
     */
    public function tearDown()
    {
        $this->pdoConnection->disconnect();
    }

    /**
     * check that the connection data is returned as expected
     *
     * @test
     */
    public function connectionData()
    {
        $this->assertSame($this->connectionData, $this->pdoConnection->getConnectionData());
    }

    /**
     * assert that a call to an undefined pdo method throws an stubDatabaseException
     *
     * @test
     * @expectedException  stubDatabaseException
     */
    public function undefinedMethod()
    {
        $this->pdoConnection->foo('bar');
    }

    /**
     * connect() can be called anytime,  but with not initial query nothing is done
     *
     * @test
     */
    public function connectCanBeCalledAnytime()
    {
        $this->mockPDO->expects($this->never())->method('query');
        $this->pdoConnection->connect();
        $this->pdoConnection->connect();
    }

    /**
     * if an initial query is present it should be executed when connection is established
     *
     * @test
     */
    public function connectShouldSetInitialQuery()
    {
        $this->connectionData->setInitialQuery('set names utf8');
        $this->mockPDO->expects($this->once())->method('query')->with($this->equalTo('set names utf8'));
        $this->pdoConnection->connect();
    }

    /**
     * assert that a call to the methods delivers the expected result
     *
     * @test
     */
    public function methods()
    {
        $this->mockPDO->expects($this->once())->method('beginTransaction')->will($this->returnValue(true));
        $this->mockPDO->expects($this->once())->method('commit')->will($this->returnValue(true));
        $this->mockPDO->expects($this->once())->method('rollBack')->will($this->returnValue(true));
        $this->mockPDO->expects($this->once())->method('exec')->with($this->equalTo('foo'))->will($this->returnValue(66));
        $this->mockPDO->expects($this->once())->method('lastInsertId')->will($this->returnValue(5));
        $this->assertTrue($this->pdoConnection->beginTransaction());
        $this->assertTrue($this->pdoConnection->commit());
        $this->assertTrue($this->pdoConnection->rollBack());
        $this->assertEquals(66, $this->pdoConnection->exec('foo'));
        $this->assertEquals(5, $this->pdoConnection->getLastInsertID());
    }

    /**
     * assert that prepare() works as expected
     *
     * @test
     */
    public function prepare()
    {
        $this->mockPDO->expects($this->once())->method('prepare')->with($this->equalTo('foo'), $this->equalTo(array()))->will($this->returnValue(new TestPDOStatement()));
        $statement = $this->pdoConnection->prepare('foo');
        $this->assertInstanceOf('stubDatabasePDOStatement', $statement);
    }

    /**
     * assert that query() works as expected
     *
     * @test
     */
    public function queryWithOutFetchMode()
    {
        $this->mockPDO->expects($this->once())->method('query')->with($this->equalTo('foo'))->will($this->returnValue(new TestPDOStatement()));
        $statement = $this->pdoConnection->query('foo');
        $this->assertInstanceOf('stubDatabasePDOStatement', $statement);
    }

    /**
     * assert that query() works as expected
     *
     * @test
     */
    public function queryWithNoSpecialFetchMode()
    {
        $this->mockPDO->expects($this->once())
                      ->method('query')
                      ->with($this->equalTo('foo'), $this->equalTo(PDO::FETCH_ASSOC))
                      ->will($this->returnValue(new TestPDOStatement()));
        $statement = $this->pdoConnection->query('foo', array('fetchMode' => PDO::FETCH_ASSOC));
        $this->assertInstanceOf('stubDatabasePDOStatement', $statement);
    }

    /**
     * assert that query() works as expected
     *
     * @test
     */
    public function queryWithFetchModeColumn()
    {
        $this->mockPDO->expects($this->once())
                      ->method('query')
                      ->with($this->equalTo('foo'), $this->equalTo(PDO::FETCH_COLUMN), $this->equalTo(5))
                      ->will($this->returnValue(new TestPDOStatement()));
        $statement = $this->pdoConnection->query('foo', array('fetchMode' => PDO::FETCH_COLUMN, 'colNo' => 5));
        $this->assertInstanceOf('stubDatabasePDOStatement', $statement);
    }

    /**
     * assert that query() works as expected
     *
     * @test
     * @expectedException  stubDatabaseException
     */
    public function queryWithFetchModeColumnButMissingOption()
    {
        $statement = $this->pdoConnection->query('foo', array('fetchMode' => PDO::FETCH_COLUMN));
    }

    /**
     * assert that query() works as expected
     *
     * @test
     */
    public function queryWithFetchModeInto()
    {
        $class = new stdClass();
        $this->mockPDO->expects($this->once())
                      ->method('query')
                      ->with($this->equalTo('foo'), $this->equalTo(PDO::FETCH_INTO), $this->equalTo($class))
                      ->will($this->returnValue(new TestPDOStatement()));
        $statement = $this->pdoConnection->query('foo', array('fetchMode' => PDO::FETCH_INTO, 'object' => $class));
        $this->assertInstanceOf('stubDatabasePDOStatement', $statement);
    }

    /**
     * assert that query() works as expected
     *
     * @test
     * @expectedException  stubDatabaseException
     */
    public function queryWithFetchModeIntoButMissingOption()
    {
        $statement = $this->pdoConnection->query('foo', array('fetchMode' => PDO::FETCH_INTO));
    }

    /**
     * assert that query() works as expected
     *
     * @test
     */
    public function queryWithFetchModeClass()
    {
        $this->mockPDO->expects($this->once())
                      ->method('query')
                      ->with($this->equalTo('foo'), $this->equalTo(PDO::FETCH_CLASS), $this->equalTo('MyClass'), $this->equalTo(array()))
                      ->will($this->returnValue(new TestPDOStatement()));
        $statement = $this->pdoConnection->query('foo', array('fetchMode' => PDO::FETCH_CLASS, 'classname' => 'MyClass'));
        $this->assertInstanceOf('stubDatabasePDOStatement', $statement);
    }

    /**
     * assert that query() works as expected
     *
     * @test
     * @expectedException  stubDatabaseException
     */
    public function queryWithFetchModeClassButMissingOption()
    {
        $statement = $this->pdoConnection->query('foo', array('fetchMode' => PDO::FETCH_CLASS));
    }

    /**
     * assert that query() works as expected
     *
     * @test
     */
    public function queryWithFetchModeClassWithCtorArgs()
    {
        $this->mockPDO->expects($this->once())
                      ->method('query')
                      ->with($this->equalTo('foo'), $this->equalTo(PDO::FETCH_CLASS), $this->equalTo('MyClass'), $this->equalTo(array('foo')))
                      ->will($this->returnValue(new TestPDOStatement()));
        $statement = $this->pdoConnection->query('foo', array('fetchMode' => PDO::FETCH_CLASS, 'classname' => 'MyClass', 'ctorargs' => array('foo')));
        $this->assertInstanceOf('stubDatabasePDOStatement', $statement);
    }

    /**
     * the database type should be returned
     *
     * @test
     */
    public function databaseTypeIsReturned()
    {
        $this->connectionData->setDSN('mysql:host=localhost;dbname=example');
        $this->assertEquals('mysql', $this->pdoConnection->getDatabase());
    }
}
?>