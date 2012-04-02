<?php
/**
 * Tests for net::stubbles::lang::stubDefaultMode.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @version     $Id: stubDefaultModeExceptionHandlerTestCase.php 3331 2012-03-02 15:21:37Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubDefaultMode',
                      'net::stubbles::lang::errorhandler::stubExceptionHandler'
);
/**
 * Mock class to be used as exception handler.
 *
 * @package     stubbles
 * @subpackage  lang_test
 */
class stubModestubExceptionHandler extends stubBaseObject implements stubExceptionHandler
{
    /**
     * path to project
     *
     * @var  string
     */
    protected $projectPath;

    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     */
    public function __construct($projectPath)
    {
        $this->projectPath = $projectPath;
    }

    /**
     * returns path to project
     *
     * @return  string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

    /**
     * handles the exception
     *
     * @param  Exception  $exception  the uncatched exception
     */
    public function handleException(Exception $exception) { }

    /**
     * handles the exception
     *
     * @param  Exception  $exception  the uncatched exception
     */
    public static function handleExceptionStatically(Exception $exception) { }
}
/**
 * Tests for net::stubbles::lang::stubDefaultMode.
 *
 * Contains all tests which require restoring the previous exception handler.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @group       lang
 */
class stubModeExceptionHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * clean up test environment
     */
    public function tearDown()
    {
        restore_exception_handler();
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     */
    public function getCallbackExceptionHandlerWithStatic()
    {
        $handler = array('class'  => 'stubModestubExceptionHandler',
                         'method' => 'handleExceptionStatically',
                         'type'   => stubMode::HANDLER_STATIC
                   );
        $mode    = new stubDefaultMode('FOO', $handler, array(), false);
        $this->assertTrue($mode->registerExceptionHandler('/tmp'));
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     */
    public function getCallbackExceptionHandlerWithInstanceFromClassname()
    {
        $handler  = array('class'  => 'stubModestubExceptionHandler',
                          'method' => 'handleException',
                          'type'   => stubMode::HANDLER_INSTANCE
                    );
        $mode     = new stubDefaultMode('FOO', $handler, array(), false);
        $instance = $mode->registerExceptionHandler('/tmp');
        $this->assertInstanceOf('stubModestubExceptionHandler', $instance);
        $this->assertEquals('/tmp', $instance->getProjectPath());
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     */
    public function getCallbackExceptionHandlerWithInstanceFromInstance()
    {
        $instance = new stubModestubExceptionHandler('/tmp');
        $handler  = array('class'  => $instance,
                          'method' => 'handleException',
                          'type'   => stubMode::HANDLER_INSTANCE
                    );
        $mode     = new stubDefaultMode('FOO', $handler, array(), false);
        $this->assertSame($instance, $mode->registerExceptionHandler('/foo'));
        $this->assertEquals('/tmp', $instance->getProjectPath());
    }

    /**
     * test that the exception handler is set correct
     *
     * @test
     */
    public function setExceptionHandler()
    {
        $mode = new stubDefaultMode('FOO', array(), array(), false);
        $mode->setExceptionHandler('stubModestubExceptionHandler', 'handleException', stubMode::HANDLER_INSTANCE);
        $instance = $mode->registerExceptionHandler('/tmp');
        $this->assertInstanceOf('stubModestubExceptionHandler', $instance);
    }
}
?>