<?php
/**
 * Tests for net::stubbles::lang::stubDefaultMode.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @version     $Id: stubDefaultModeTestCase.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubDefaultMode',
                      'net::stubbles::lang::errorhandler::stubErrorHandler'
);
/**
 * Tests for net::stubbles::lang::stubDefaultMode.
 *
 * All tests that do not require restoring the error or exception handler.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @group       lang
 */
class stubDefaultModeTestCase extends PHPUnit_Framework_TestCase
{

    /**
     * assure that creating the callback work as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function getCallbackExceptionHandlerWithStaticButInstanceGiven()
    {
        $instance = new stdClass();
        $handler  = array('class'  => $instance,
                          'method' => 'handleException',
                          'type'   => stubMode::HANDLER_STATIC
                    );
        $mode     = new stubDefaultMode('FOO', $handler, array(), false);
        $mode->registerExceptionHandler('/tmp');
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function getCallbackErrorHandlerWithStaticButInstanceGiven()
    {
        $instance = new stdClass();
        $handler  = array('class'  => $instance,
                          'method' => 'handle',
                          'type'   => stubMode::HANDLER_STATIC
                    );
        $mode     = new stubDefaultMode('FOO', array(), $handler, false);
        $mode->registerErrorHandler('/tmp');
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     */
    public function noErrorHandler()
    {
        $mode = new stubDefaultMode('FOO', array(), array(), false);
        $this->assertFalse($mode->registerErrorHandler('/tmp'));
    }

    /**
     * assure that creating the callback work as expected
     *
     * @test
     */
    public function noExceptionHandler()
    {
        $mode = new stubDefaultMode('FOO', array(), array(), false);
        $this->assertFalse($mode->registerExceptionHandler('/tmp'));
    }

    /**
     * test that cache switch is set correct
     *
     * @test
     */
    public function cacheEnabled()
    {
        $this->assertTrue(stubDefaultMode::prod()->isCacheEnabled());
        $this->assertTrue(stubDefaultMode::test()->isCacheEnabled());
        $this->assertFalse(stubDefaultMode::stage()->isCacheEnabled());
        $this->assertFalse(stubDefaultMode::dev()->isCacheEnabled());
    }

    /**
     * test that the stage and dev mode do not register any error handler by default
     *
     * @test
     */
    public function noErrorHandlerForStageAndDevMode()
    {
        $this->assertFalse(stubDefaultMode::stage()->registerErrorHandler('/tmp'));
        $this->assertFalse(stubDefaultMode::dev()->registerErrorHandler('/tmp'));
    }
}
?>