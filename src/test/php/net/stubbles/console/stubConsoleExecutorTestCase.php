<?php
/**
 * Test for net::stubbles::console::stubConsoleExecutor.
 *
 * @package     stubbles
 * @subpackage  console_test
 * @version     $Id: stubConsoleExecutorTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::console::stubConsoleExecutor');
/**
 * Test for net::stubbles::console::stubConsoleExecutor.
 *
 * @package     stubbles
 * @subpackage  console_test
 * @group       console
 */
class stubConsoleExecutorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubConsoleExecutor
     */
    protected $executor;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->executor = new stubConsoleExecutor();
    }

    /**
     * redirectTo() should return itself
     *
     * @test
     */
    public function redirectToReturnsItself()
    {
        $this->assertSame($this->executor, $this->executor->redirectTo('2>&1'));
    }

    /**
     * execute() without output stream set
     *
     * @test
     */
    public function executeWithoutOutputStream()
    {
        $this->assertNull($this->executor->getOutputStream());
        $this->assertSame($this->executor, $this->executor->execute('echo foo'));
    }

    /**
     * execute() with former output stream set
     *
     * @test
     */
    public function executeWithOutputStream()
    {
        $mockOutputStream = $this->getMock('stubOutputStream');
        $mockOutputStream->expects($this->once())
                         ->method('writeLine')
                         ->with($this->equalTo('foo'));
        $this->assertSame($this->executor, $this->executor->streamOutputTo($mockOutputStream));
        $this->assertSame($mockOutputStream, $this->executor->getOutputStream());
        $this->assertSame($this->executor, $this->executor->execute('echo foo'));
    }

    /**
     * execute() fails and throws an exception
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function executeFails()
    {
        $this->executor->execute('php -r "throw new Exception();"');
    }

    /**
     * executeAsync() returns input stream
     *
     * @test
     */
    public function executeAsync()
    {
        $commandInputStream = $this->executor->executeAsync('echo foo');
        $this->assertInstanceOf('stubCommandInputStream', $commandInputStream);
        $this->assertEquals('foo', chop($commandInputStream->read()));
    }

    /**
     * executeAsync() fails and throws an exception
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function executeAsyncFails()
    {
        $commandInputStream = $this->executor->executeAsync('php -r "throw new Exception();"');
        $this->assertInstanceOf('stubCommandInputStream', $commandInputStream);
        while ($commandInputStream->eof() === false) {
            $commandInputStream->readLine();
        }
        
        $commandInputStream->close();
    }

    /**
     * illegal resource for command input stream throws exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalResourceForCommandInputStream()
    {
        new stubCommandInputStream('invalid');
    }

    /**
     * reading after close throws exception
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function readAfterCloseThrowsException()
    {
        $commandInputStream = $this->executor->executeAsync('echo foo');
        $this->assertInstanceOf('stubCommandInputStream', $commandInputStream);
        $this->assertEquals('foo', chop($commandInputStream->read()));
        $commandInputStream->close();
        $commandInputStream->read();
    }

    /**
     * executeDirect() returns input stream
     *
     * @test
     */
    public function executeDirect()
    {
        $this->assertEquals(array('foo'), $this->executor->executeDirect('echo foo'));
    }

    /**
     * executeDirect() fails and throws an exception
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function executeDirectFails()
    {
        $this->executor->executeDirect('php -r "throw new Exception();"');
    }

}
?>