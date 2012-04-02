<?php
/**
 * Test for net::stubbles::console::stubConsoleCommandRunner.
 *
 * @package     stubbles
 * @subpackage  console_test
 * @version     $Id: stubConsoleCommandRunnerTestCase.php 2240 2009-06-16 21:50:52Z mikey $
 */
stubClassLoader::load('net::stubbles::console::stubConsoleCommandRunner');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  console_test
 */
class stubConsoleCommandRunnerTest extends stubBaseObject implements stubConsoleCommand
{
    /**
     * exception to be thrown
     *
     * @var  Exception
     */
    public static $exception;

    /**
     * runs the command and returns an exit code
     *
     * @return  int
     */
    public function run()
    {
        if (null !== self::$exception) {
            throw self::$exception;
        }
        
        return 313;
    }
}
/**
 * Test for net::stubbles::console::stubConsoleCommandRunner.
 *
 * @package     stubbles
 * @subpackage  console_test
 * @group       console
 */
class stubConsoleCommandRunnerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * mocked output stream
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockOutputStream                  = $this->getMock('stubOutputStream');
        stubConsoleCommandRunnerTest::$exception = null;
    }

    /**
     * missing classname gives error message and returns
     *
     * @test
     */
    public function missingClassnameGivesErrorMessageAndReturns()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine');
        $this->assertEquals(1, stubConsoleCommandRunner::run('projectPath', array(), $this->mockOutputStream));
    }

    /**
     * thrown application exception is catched
     *
     * @test
     */
    public function thrownApplicationExceptionIsCatched()
    {
        stubConsoleCommandRunnerTest::$exception = new Exception('failure');
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo('*** Exception: failure'));
        $this->assertEquals(70, stubConsoleCommandRunner::run('projectPath',
                                                              array('stubcli', 'projectPath', 'stubConsoleCommandRunnerTest'),
                                                              $this->mockOutputStream
                                 )
        );
    }

    /**
     * thrown application stubException is catched
     *
     * @test
     */
    public function thrownApplicationStubExceptionIsCatched()
    {
        stubConsoleCommandRunnerTest::$exception = new stubException('failure');
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo('*** net::stubbles::lang::exceptions::stubException: failure'));
        $this->assertEquals(70, stubConsoleCommandRunner::run('projectPath',
                                                              array('stubcli', 'projectPath', 'stubConsoleCommandRunnerTest'),
                                                              $this->mockOutputStream
                                )
        );
    }

    /**
     * thrown application stubException is catched
     *
     * @test
     */
    public function commandReturnValueIsReturned()
    {
        $this->assertEquals(313, stubConsoleCommandRunner::main('projectPath',
                                                                array('stubcli', 'projectPath', 'stubConsoleCommandRunnerTest')
                                 )
        );
    }
}
?>