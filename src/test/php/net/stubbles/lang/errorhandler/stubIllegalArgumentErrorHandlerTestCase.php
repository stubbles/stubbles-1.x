<?php
/**
 * Tests for net::stubbles::lang::errorhandler::stubIllegalArgumentErrorHandler
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @version     $Id: stubIllegalArgumentErrorHandlerTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubIllegalArgumentErrorHandler');
/**
 * Tests for net::stubbles::lang::errorhandler::stubIllegalArgumentErrorHandler
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @group       lang
 * @group       lang_errorhandler
 */
class stubIllegalArgumentErrorHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubIllegalArgumentErrorHandler
     */
    protected $illegalArgumentErrorHandler;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->illegalArgumentErrorHandler = new stubIllegalArgumentErrorHandler();
    }

    /**
     * assure that isResponsible() works correct
     *
     * @test
     */
    public function isResponsible()
    {
        $this->assertFalse($this->illegalArgumentErrorHandler->isResponsible(E_NOTICE, 'foo'));
        $this->assertFalse($this->illegalArgumentErrorHandler->isResponsible(E_RECOVERABLE_ERROR, 'foo'));
        $this->assertTrue($this->illegalArgumentErrorHandler->isResponsible(E_RECOVERABLE_ERROR, 'Argument 1 passed to Class::method() must be an instance of AnotherClass, string given'));
    }

    /**
     * assure that isSupressable() works correct
     *
     * @test
     */
    public function isSupressable()
    {
        $this->assertFalse($this->illegalArgumentErrorHandler->isSupressable(E_RECOVERABLE_ERROR, 'foo'));
    }

    /**
     * assure that handle() works correct
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function handle()
    {
        $this->illegalArgumentErrorHandler->handle(E_RECOVERABLE_ERROR, 'foo');
    }
}
?>