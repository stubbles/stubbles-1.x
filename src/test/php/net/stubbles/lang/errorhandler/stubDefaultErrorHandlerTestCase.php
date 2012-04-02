<?php
/**
 * Tests for net::stubbles::lang::errorhandler::stubDefaultErrorHandler.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @version     $Id: stubDefaultErrorHandlerTestCase.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubDefaultErrorHandler');
/**
 * Tests for net::stubbles::lang::errorhandler::stubDefaultErrorHandler.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @group       lang
 * @group       lang_errorhandler
 */
class stubDefaultErrorHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDefaultErrorHandler
     */
    protected $defaultErrorHandler;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultErrorHandler = new stubDefaultErrorHandler('/tmp');
    }

    /**
     * assert that all registered handlers are returned
     *
     * @test
     */
    public function getHandlers()
    {
        $errorHandlers = $this->defaultErrorHandler->getErrorHandlers();
        $this->assertInstanceOf('stubIllegalArgumentErrorHandler', $errorHandlers[0]);
        $this->assertInstanceOf('stubLogErrorHandler', $errorHandlers[1]);
    }
}
?>