<?php
/**
 * Tests for net::stubbles::lang::stubDefaultMode.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @version     $Id: stubDefaultModeErrorHandlerTestCase.php 3331 2012-03-02 15:21:37Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubDefaultMode',
                      'net::stubbles::lang::errorhandler::stubErrorHandler'
);
/**
 * Mock class to be used as error handler.
 *
 * @package     stubbles
 * @subpackage  lang_test
 */
class stubModestubErrorHandler extends stubBaseObject implements stubErrorHandler
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
     * checks whether this error handler is responsible for the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error handler is responsible, else false
     */
    public function isResponsible($level, $message, $file = null, $line = null, array $context = array()) {}

    /**
     * checks whether this error is supressable
     *
     * This method is called in case the level is 0. It decides whether the
     * error has to be handled or if it can be omitted.
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error is supressable, else false
     */
    public function isSupressable($level, $message, $file = null, $line = null, array $context = array()) {}

    /**
     * handles the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error message should populate $php_errormsg, else false
     * @throws  stubException  error handlers are allowed to throw every exception they want to
     */
    public function handle($level, $message, $file = null, $line = null, array $context = array()) {}

    /**
     * handles the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error message should populate $php_errormsg, else false
     * @throws  stubException  error handlers are allowed to throw every exception they want to
     */
    public static function handleStatic($level, $message, $file = null, $line = null, array $context = array()) {}
}
/**
 * Tests for net::stubbles::lang::stubDefaultMode.
 *
 * Contains all tests which require restoring the previous error handler.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @group       lang
 */
class stubModeErrorHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * clean up test environment
     */
    public function tearDown()
    {
        restore_error_handler();
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     */
    public function getCallbackErrorHandlerWithStatic()
    {
        $handler = array('class'  => 'stubModestubErrorHandler',
                         'method' => 'handleStatic',
                         'type'   => stubMode::HANDLER_STATIC
                   );
        $mode    = new stubDefaultMode('FOO', array(), $handler, false);
        $this->assertTrue($mode->registerErrorHandler('/tmp'));
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     */
    public function getCallbackErrorHandlerWithInstanceFromClassname()
    {
        $handler  = array('class'  => 'stubModestubErrorHandler',
                          'method' => 'handle',
                          'type'   => stubMode::HANDLER_INSTANCE
                    );
        $mode     = new stubDefaultMode('FOO', array(), $handler, false);
        $instance = $mode->registerErrorHandler('/tmp');
        $this->assertInstanceOf('stubModestubErrorHandler', $instance);
        $this->assertEquals('/tmp', $instance->getProjectPath());
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     */
    public function getCallbackErrorHandlerWithInstanceFromInstance()
    {
        $instance = new stubModestubErrorHandler('/tmp');
        $handler  = array('class'  => $instance,
                          'method' => 'handle',
                          'type'   => stubMode::HANDLER_INSTANCE
                    );
        $mode     = new stubDefaultMode('FOO', array(), $handler, false);
        $this->assertSame($instance, $mode->registerErrorHandler('/foo'));
        $this->assertEquals('/tmp', $instance->getProjectPath());
    }

    /**
     * test that the error handler is set correct
     *
     * @test
     */
    public function setErrorHandler()
    {
        $mode = new stubDefaultMode('FOO', array(), array(), false);
        $mode->setErrorHandler('stubModestubErrorHandler', 'handleStatic', stubMode::HANDLER_STATIC);
        $this->assertTrue($mode->registerErrorHandler('/tmp'));
    }
}
?>