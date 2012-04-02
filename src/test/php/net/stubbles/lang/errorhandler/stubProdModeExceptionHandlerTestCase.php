<?php
/**
 * Tests for net::stubbles::lang::errorhandler::stubProdModeExceptionHandler.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @version     $Id: stubProdModeExceptionHandlerTestCase.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubProdModeExceptionHandler');
@include_once 'vfsStream/vfsStream.php';
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 */
class TeststubProdModeExceptionHandler extends stubProdModeExceptionHandler
{
    /**
     * direct access to protected method
     *
     * @param  stubResponse  $response   response to be send
     * @param  Exception     $exception  the uncatched exception
     */
    public function callFillResponse(stubResponse $response, Exception $exception)
    {
        $this->fillResponse($response, $exception);
    }
}
/**
 * Tests for net::stubbles::lang::errorhandler::stubProdModeExceptionHandler.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @group       lang
 * @group       lang_errorhandler
 */
class stubProdModeExceptionHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubProdModeExceptionHandler
     */
    protected $prodModeExceptionHandler;
    /**
     * mocked response instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * root path for log files
     *
     * @var  stubVfsStreamDirectory
     */
    protected $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped(__CLASS__ . ' requires vfsStream, see http://vfs.bovigo.org/');
        }

        $this->root                     = vfsStream::setup();
        $this->prodModeExceptionHandler = new TeststubProdModeExceptionHandler(vfsStream::url('root'));
        $this->mockResponse             = $this->getMock('stubResponse');
    }

    /**
     * @test
     */
    public function createsStatusCode500()
    {
        $this->mockResponse->expects($this->once())->method('setStatusCode')->with($this->equalTo(500));
        $this->prodModeExceptionHandler->callFillResponse($this->mockResponse, new Exception('message'));
    }

    /**
     * @test
     */
    public function doesNotWriteBodyIfNoError500FilePresent()
    {
        $this->mockResponse->expects($this->never())->method('write');
        $this->prodModeExceptionHandler->callFillResponse($this->mockResponse, new Exception('message'));
    }

    /**
     * @test
     */
    public function doesNWriteBodyIfError500FilePresent()
    {
        vfsStream::newFile('docroot/500.html')
                 ->withContent('An error occurred')
                 ->at($this->root);
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('An error occurred'));
        $this->prodModeExceptionHandler->callFillResponse($this->mockResponse, new Exception('message'));
    }
}
?>