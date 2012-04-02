<?php
/**
 * Test for net::stubbles::rdbms::pdo::stubDatabasePDOStatement.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @version     $Id: stubDatabasePDOStatementTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::pdo::stubDatabasePDOStatement');
/**
 * Test for net::stubbles::rdbms::pdo::stubDatabasePDOStatement.
 *
 * @package     stubbles
 * @subpackage  rdbms_test
 * @group       rdbms
 * @group       rdbms_pdo
 */
class stubDatabasePDOStatementTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabasePDOStatement
     */
    protected $pdoStatement;
    /**
     * mock for pdo statement
     *
     * @var  SimpleMock
     */
    protected $mockPDOStatement;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('pdo') === false) {
            $this->markTestSkipped('net::stubbles::rdbms::pdo::stubDatabasePDOStatement requires PHP-extension "pdo".');
        } elseif (version_compare(phpversion(), '5.2.5', '<') === true) {
            $this->markTestSkipped('net::stubbles::rdbms::pdo::stubDatabasePDOStatement can not be tested because PDOStatement can not be mocked, see http://bugs.php.net/bug.php?id=42452. Please use a PHP version greater or equal to 5.2.5 to run this test.');
        }
        
        $this->mockPDOStatement = $this->getMock('PDOStatement');
        $this->pdoStatement     = new stubDatabasePDOStatement($this->mockPDOStatement);
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function bindColumn()
    {
        $bar = 1;
        $this->mockPDOStatement->expects($this->exactly(2))
                               ->method('bindColumn')
                               ->with($this->equalTo('foo'), $this->equalTo($bar), $this->equalTo(PDO::PARAM_INT))
                               ->will($this->onConsecutiveCalls(true, false));
        $this->assertTrue($this->pdoStatement->bindColumn('foo', $bar, PDO::PARAM_INT));
        $this->assertFalse($this->pdoStatement->bindColumn('foo', $bar, PDO::PARAM_INT));
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function bindParam()
    {
        $bar = 1;
        $this->mockPDOStatement->expects($this->exactly(2))
                               ->method('bindParam')
                               ->with($this->equalTo('foo'), $this->equalTo($bar), $this->equalTo(PDO::PARAM_INT))
                               ->will($this->onConsecutiveCalls(true, false));
        $this->assertTrue($this->pdoStatement->bindParam('foo', $bar, PDO::PARAM_INT, 2));
        $this->assertFalse($this->pdoStatement->bindParam('foo', $bar, PDO::PARAM_INT, 2));
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function bindValue()
    {
        $this->mockPDOStatement->expects($this->exactly(2))
                               ->method('bindValue')
                               ->with($this->equalTo('foo'), $this->equalTo(1), $this->equalTo(PDO::PARAM_INT))
                               ->will($this->onConsecutiveCalls(true, false));
        $this->assertTrue($this->pdoStatement->bindValue('foo', 1, PDO::PARAM_INT));
        $this->assertFalse($this->pdoStatement->bindValue('foo', 1, PDO::PARAM_INT));
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function execute()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('execute')
                               ->with($this->equalTo(array()))
                               ->will($this->returnValue(true));
        $result = $this->pdoStatement->execute(array());
        $this->assertInstanceOf('stubDatabaseResult', $result);
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     * @expectedException  stubDatabaseException
     */
    public function executeFails()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('execute')
                               ->with($this->equalTo(array()))
                               ->will($this->returnValue(false));
        $this->pdoStatement->execute(array());
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function fetch()
    {
        $this->mockPDOStatement->expects($this->at(0))
                               ->method('fetch')
                               ->with($this->equalTo(PDO::FETCH_BOTH), $this->equalTo(null), $this->equalTo(null))
                               ->will($this->returnValue(true));
        $this->mockPDOStatement->expects($this->at(1))
                               ->method('fetch')
                               ->with($this->equalTo(PDO::FETCH_ASSOC), $this->equalTo('foo'), $this->equalTo(null))
                               ->will($this->returnValue(false));
        $this->mockPDOStatement->expects($this->at(2))
                               ->method('fetch')
                               ->with($this->equalTo(PDO::FETCH_OBJ), $this->equalTo(null), $this->equalTo(50))
                               ->will($this->returnValue(array()));
        $this->mockPDOStatement->expects($this->at(3))
                               ->method('fetch')
                               ->with($this->equalTo(PDO::FETCH_BOTH), $this->equalTo('foo'), $this->equalTo(50))
                               ->will($this->returnValue(50));
        $this->assertTrue($this->pdoStatement->fetch());
        $this->assertFalse($this->pdoStatement->fetch(PDO::FETCH_ASSOC, array('cursorOrientation' => 'foo')));
        $this->assertEquals(array(), $this->pdoStatement->fetch(PDO::FETCH_OBJ, array('cursorOffset' => 50)));
        $this->assertEquals(50, $this->pdoStatement->fetch(PDO::FETCH_BOTH, array('cursorOrientation' => 'foo',
                                                                                  'cursorOffset'      => 50,
                                                                                 'foo'               => 'bar'
                                                                            )
                                ) 
        );
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function fetchOne()
    {
        $this->mockPDOStatement->expects($this->at(0))
                               ->method('fetchColumn')
                               ->with($this->equalTo(0))
                               ->will($this->returnValue(true));
        $this->mockPDOStatement->expects($this->at(1))
                               ->method('fetchColumn')
                               ->with($this->equalTo(5))
                               ->will($this->returnValue(false));
        $this->assertTrue($this->pdoStatement->fetchOne());
        $this->assertFalse($this->pdoStatement->fetchOne(5));
    }

    /**
     * @test
     */
    public function fetchAllWithoutArguments()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('fetchAll')
                               ->will($this->returnValue(array()));
        $this->assertEquals(array(), $this->pdoStatement->fetchAll());
    }

    /**
     * @test
     * @group  bug248
     */
    public function fetchAllWithFetchColumnUsesColumnZeroIsDefault()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('fetchAll')
                               ->with($this->equalTo(PDO::FETCH_COLUMN), $this->equalTo(0))
                               ->will($this->returnValue(array()));
        $this->assertEquals(array(), $this->pdoStatement->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * @test
     * @group  bug248
     */
    public function fetchAllWithFetchColumnUsesGivenColumn()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('fetchAll')
                               ->with($this->equalTo(PDO::FETCH_COLUMN), $this->equalTo(2))
                               ->will($this->returnValue(array()));
        $this->assertEquals(array(), $this->pdoStatement->fetchAll(PDO::FETCH_COLUMN, array('columnIndex' => 2)));
    }

    /**
     * @test
     */
    public function fetchAllWithFetchObject()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('fetchAll')
                               ->with($this->equalTo(PDO::FETCH_OBJ))
                               ->will($this->returnValue(array()));
        $this->assertEquals(array(), $this->pdoStatement->fetchAll(PDO::FETCH_OBJ));
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     * @since  1.3.2
     * @group  bug248
     */
    public function fetchAllWithFetchClassWithoutClassThrowsIllegalArgumentException()
    {
        $this->mockPDOStatement->expects($this->never())
                               ->method('fetchAll');
        $this->pdoStatement->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * @test
     * @since  1.3.2
     * @group  bug248
     */
    public function fetchAllWithFetchClassWithoutArguments()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('fetchAll')
                               ->with($this->equalTo(PDO::FETCH_CLASS),
                                      $this->equalTo('ExampleClass'),
                                      $this->equalTo(null)
                                 )
                               ->will($this->returnValue(array()));
        $this->assertEquals(array(),
                            $this->pdoStatement->fetchAll(PDO::FETCH_CLASS,
                                                          array('classname' => 'ExampleClass')
                            )
        );
    }

    /**
     * @test
     * @since  1.3.2
     * @group  bug248
     */
    public function fetchAllWithFetchClassWithArguments()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('fetchAll')
                               ->with($this->equalTo(PDO::FETCH_CLASS),
                                      $this->equalTo('ExampleClass'),
                                      $this->equalTo('foo')
                                 )
                               ->will($this->returnValue(array()));
        $this->assertEquals(array(),
                            $this->pdoStatement->fetchAll(PDO::FETCH_CLASS,
                                                          array('classname' => 'ExampleClass',
                                                                'arguments' => 'foo'
                                                          )
                            )
        );
    }

    /**
     * @test
     * @since  1.3.2
     * @group  bug248
     */
    public function fetchAllWithFetchFunc()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('fetchAll')
                               ->with($this->equalTo(PDO::FETCH_FUNC),
                                      $this->equalTo('exampleFunc')
                                 )
                               ->will($this->returnValue(array()));
        $this->assertEquals(array(),
                            $this->pdoStatement->fetchAll(PDO::FETCH_FUNC,
                                                          array('function' => 'exampleFunc')
                            )
        );
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     * @since  1.3.2
     * @group  bug248
     */
    public function fetchAllWithFetchFuncWithMissingFunctionThrowsIllegalArgumentException()
    {
        $this->mockPDOStatement->expects($this->never())
                               ->method('fetchAll');
        $this->pdoStatement->fetchAll(PDO::FETCH_FUNC);
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function next()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('nextRowset')
                               ->will($this->returnValue(true));
        $this->assertTrue($this->pdoStatement->next());
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function rowCount()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('rowCount')
                               ->will($this->returnValue(5));
        $this->assertEquals($this->pdoStatement->count(), 5);
    }

    /**
     * check that method arguments and return values are passed correct
     *
     * @test
     */
    public function free()
    {
        $this->mockPDOStatement->expects($this->once())
                               ->method('closeCursor')
                               ->will($this->returnValue(true));
        $this->assertTrue($this->pdoStatement->free());
    }
}
?>