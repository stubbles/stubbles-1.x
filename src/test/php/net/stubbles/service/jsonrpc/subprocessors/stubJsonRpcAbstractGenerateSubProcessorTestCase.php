<?php
/**
 * Test for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcAbstractGenerateSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors_test
 * @version     $Id: stubJsonRpcAbstractGenerateSubProcessorTestCase.php 2683 2010-08-24 19:33:16Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder',
                      'net::stubbles::lang::stubDefaultMode',
                      'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcAbstractGenerateSubProcessor'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors_test
 */
class TeststubJsonRpcAbstractGenerateSubProcessor extends stubJsonRpcAbstractGenerateSubProcessor
{
    /**
     * does the processing of the subtask
     *
     * @param  stubRequest     $request   current request
     * @param  stubSession     $session   current session
     * @param  stubResponse    $response  current response
     * @param  stubInjector    $injector  injector instance
     * @param  stubProperties  $config    json-rpc config
     */
    public function process(stubRequest $request, stubSession $session, stubResponse $response, stubInjector $injector, stubProperties $config)
    {
        // intentionally empty
    }

    /**
     * access to protected method
     *
     * @param   stubRequest  $request
     * @return  string
     */
    public function callGetServiceUrl(stubRequest $request)
    {
        return $this->getServiceUrl($request);
    }

    /**
     * access to protected method
     *
     * @param  stubInjector  $injector
     * @param  Exception     $exception
     * @param  stubResponse  $response
     * @param  string        $introduction
     */
    public function callHandleException(stubInjector $injector, Exception $exception, stubResponse $response, $introduction)
    {
        $this->handleException($injector, $exception, $response, $introduction);
    }
}
/**
 * Tests for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcAbstractGenerateSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @group       service_jsonrpc
 */
class stubJsonRpcAbstractGenerateSubProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * mocked request to use
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked response instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * instance to test
     *
     * @var  TeststubJsonRpcAbstractGenerateSubProcessor
     */
    protected $jsonRpcAbstractGenerateSubProcessor;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest                         = $this->getMock('stubRequest');
        $this->mockResponse                        = $this->getMock('stubResponse');
        $this->jsonRpcAbstractGenerateSubProcessor = new TeststubJsonRpcAbstractGenerateSubProcessor();
    }

    /**
     * retrieve service url
     *
     * @test
     */
    public function serviceUrlWithoutProcessor()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getURI')
                          ->will($this->returnValue('example.com/foo.php'));
        $this->mockRequest->expects($this->once())
                          ->method('hasParam')
                          ->will($this->returnValue(false));
        $this->assertEquals('//example.com/foo.php',
                            $this->jsonRpcAbstractGenerateSubProcessor->callGetServiceUrl($this->mockRequest)
        );
    }

    /**
     * retrieve service url
     *
     * @test
     */
    public function serviceUrlWithProcessor()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getURI')
                          ->will($this->returnValue('example.com/foo.php'));
        $this->mockRequest->expects($this->once())
                          ->method('hasParam')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'processor',
                                                                                  'jsonrpc'
                                                    )
                                 )
                            );
        $this->assertEquals('//example.com/foo.php?processor=jsonrpc',
                            $this->jsonRpcAbstractGenerateSubProcessor->callGetServiceUrl($this->mockRequest)
        );
    }

    /**
     * test exception handling in no mode
     *
     * @test
     */
    public function handleExceptionInNoMode()
    {
        $exception = new Exception('exceptionMessage');
        $this->mockResponse->expects($this->never())->method('write');
        $this->jsonRpcAbstractGenerateSubProcessor->callHandleException(new stubInjector(), $exception, $this->mockResponse, 'introduction');
    }

    /**
     * test exception handling in prod mode
     *
     * @test
     */
    public function handleExceptionInProdMode()
    {
        $binder = new stubBinder();
        $binder->bind('stubMode')->toInstance(stubDefaultMode::prod());
        $exception = new Exception('exceptionMessage');
        $this->mockResponse->expects($this->never())->method('write');
        $this->jsonRpcAbstractGenerateSubProcessor->callHandleException($binder->getInjector(), $exception, $this->mockResponse, 'introduction');
    }

    /**
     * test exception handling in non-prod mode
     *
     * @test
     */
    public function handleExceptionInOtherMode()
    {
        $binder = new stubBinder();
        $binder->bind('stubMode')->toInstance(stubDefaultMode::dev());
        $exception = new Exception('exceptionMessage');
        $this->mockResponse->expects($this->exactly(2))->method('write');
        $this->jsonRpcAbstractGenerateSubProcessor->callHandleException($binder->getInjector(), $exception, $this->mockResponse, 'introduction');
    }
}
?>