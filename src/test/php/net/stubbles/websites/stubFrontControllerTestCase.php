<?php
/**
 * Tests for net::stubbles::websites::stubFrontController.
 *
 * @package     stubbles
 * @subpackage  websites_test
 * @version     $Id: stubFrontControllerTestCase.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::stubFrontController');
/**
 * Tests for net::stubbles::websites::stubFrontController.
 *
 * @package     stubbles
 * @subpackage  websites_test
 * @group       websites
 */
class stubFrontControllerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  stubFrontController
     */
    protected $frontController;
    /**
     * mocked interceptor initializer
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInterceptorInitializer;
    /**
     * access to request
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * access to session
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
    /**
     * access to response
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * the mocked resolver
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockProcessorResolver;
    /**
     * the mocked processor
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockProcessor;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockProcessorResolver  = $this->getMock('stubProcessorResolver');
        $this->mockProcessor          = $this->getMock('stubProcessor');
        $this->mockProcessorResolver->expects($this->any())
                                    ->method('resolve')
                                    ->will($this->returnValue($this->mockProcessor));
        $this->mockInterceptorInitializer = $this->getMock('stubInterceptorInitializer');
        $this->mockInterceptorInitializer->expects($this->any())
                                         ->method('setDescriptor')
                                         ->will($this->returnValue($this->mockInterceptorInitializer));
        $this->mockRequest     = $this->getMock('stubRequest');
        $this->mockSession     = $this->getMock('stubSession');
        $this->mockResponse    = $this->getMock('stubResponse');
        $this->frontController = new stubFrontController($this->mockRequest,
                                                         $this->mockSession,
                                                         $this->mockResponse,
                                                         $this->mockProcessorResolver,
                                                         $this->mockInterceptorInitializer
                                 );
    }

    /**
     * make sure annotations are present
     *
     * @test
     */
    public function annotationsPresent()
    {
        $refClass = $this->frontController->getClass();
        $this->assertTrue($refClass->getConstructor()->hasAnnotation('Inject'));
    }

    /**
     * mock up session data replacement
     */
    protected function sessionDataReplacement()
    {
        $this->mockResponse->expects($this->once())
                           ->method('getBody')
                           ->will($this->returnValue('foo$SIDbar$SESSION_NAMEbaz$SESSION_IDdummy'));
        $this->mockResponse->expects($this->once())
                           ->method('replaceBody')
                           ->with($this->equalTo('foosid=ac1704barsidbazac1704dummy'));
        $this->mockSession->expects($this->exactly(2))
                          ->method('getId')
                          ->will($this->returnValue('ac1704'));
        $this->mockSession->expects($this->exactly(2))
                          ->method('getName')
                          ->will($this->returnValue('sid'));
    }

    /**
     * assure that processing ends when request is cancelled
     *
     * @test
     */
    public function processWithAlreadyCancelledRequest()
    {
        $this->mockRequest->expects($this->once())->method('isCancelled')->will($this->returnValue(true));
        $this->mockResponse->expects($this->once())->method('send');
        $this->mockProcessor->expects($this->never())->method('forceSsl');
        $this->mockInterceptorInitializer->expects($this->never())->method('getPreInterceptors');
        $this->mockInterceptorInitializer->expects($this->never())->method('getPostInterceptors');
        $this->mockProcessor->expects($this->never())->method('getInterceptorDescriptor');
        $this->mockProcessor->expects($this->never())->method('startup');
        $this->mockProcessor->expects($this->never())->method('process');
        $this->mockProcessor->expects($this->never())->method('cleanup');
        $this->sessionDataReplacement();
        $this->frontController->process();
    }

    /**
     * assure that processing ends when request is cancelled
     *
     * @test
     */
    public function processWithPreInterceptorCancellingRequest()
    {
        $this->mockProcessor->expects($this->any())->method('forceSsl')->will($this->returnValue(false));
        $preInterceptor1 = $this->getMock('stubPreInterceptor');
        $preInterceptor1->expects($this->once())->method('preProcess');
        $preInterceptor2 = $this->getMock('stubPreInterceptor');
        $preInterceptor2->expects($this->never())->method('preProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array($preInterceptor1, $preInterceptor2)));
        $this->mockInterceptorInitializer->expects($this->never())->method('getPostInterceptors');
        $this->mockRequest->expects($this->exactly(2))->method('isCancelled')->will($this->onConsecutiveCalls(false, true));
        $this->mockResponse->expects($this->once())->method('send');
        $this->mockProcessorResolver->expects($this->once())
                                    ->method('getInterceptorDescriptor')
                                    ->will($this->returnValue('interceptors'));
        $this->mockProcessor->expects($this->never())->method('startup');
        $this->mockProcessor->expects($this->never())->method('process');
        $this->mockProcessor->expects($this->never())->method('cleanup');
        $this->sessionDataReplacement();
        $this->frontController->process();
    }

    /**
     * assure that processing ends when request is cancelled
     *
     * @test
     */
    public function processWithProcessorCancellingRequest()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/')));
        $this->mockProcessor->expects($this->any())->method('forceSsl')->will($this->returnValue(false));
        $postInterceptor1 = $this->getMock('stubPostInterceptor');
        $postInterceptor1->expects($this->never())->method('postProcess');
        $postInterceptor2 = $this->getMock('stubPostInterceptor');
        $postInterceptor2->expects($this->never())->method('postProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array()));
        $this->mockInterceptorInitializer->expects($this->never())
                                         ->method('getPostInterceptors');
        $this->mockRequest->expects($this->exactly(2))->method('isCancelled')->will($this->onConsecutiveCalls(false, true));
        $this->mockProcessor->expects($this->once())->method('startup');
        $this->mockProcessor->expects($this->once())->method('process');
        $this->mockProcessor->expects($this->once())->method('cleanup');
        $this->mockResponse->expects($this->once())->method('send');
        $this->sessionDataReplacement();
        $this->frontController->process();
    }

    /**
     * @test
     */
    public function processWithProcessorThrowingException()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/')));
        $this->mockProcessor->expects($this->any())->method('forceSsl')->will($this->returnValue(false));
        $this->mockProcessor->expects($this->once())->method('startup');
        $this->mockProcessor->expects($this->any())
                            ->method('process')
                            ->will($this->throwException(new stubProcessorException(500, 'Internal Server Error')));
        $this->mockProcessor->expects($this->once())->method('cleanup');
        $postInterceptor1 = $this->getMock('stubPostInterceptor');
        $postInterceptor1->expects($this->never())->method('postProcess');
        $postInterceptor2 = $this->getMock('stubPostInterceptor');
        $postInterceptor2->expects($this->never())->method('postProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array()));
        $this->mockInterceptorInitializer->expects($this->never())
                                         ->method('getPostInterceptors');
        $this->mockRequest->expects($this->exactly(2))->method('isCancelled')->will($this->onConsecutiveCalls(false, true));
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo(500));
        $this->mockResponse->expects($this->once())->method('send');
        $this->sessionDataReplacement();
        $this->frontController->process();
    }

    /**
     * @test
     */
    public function processWithProcessorResolverThrowingExceptionOnProcessorCreation()
    {
        $this->mockProcessorResolver  = $this->getMock('stubProcessorResolver');
        $this->mockProcessorResolver->expects($this->any())
                                    ->method('resolve')
                                    ->will($this->throwException(new stubProcessorException(404, 'Not Found')));
        $this->frontController = new stubFrontController($this->mockRequest,
                                                         $this->mockSession,
                                                         $this->mockResponse,
                                                         $this->mockProcessorResolver,
                                                         $this->mockInterceptorInitializer
                                 );
        $postInterceptor1 = $this->getMock('stubPostInterceptor');
        $postInterceptor1->expects($this->never())->method('postProcess');
        $postInterceptor2 = $this->getMock('stubPostInterceptor');
        $postInterceptor2->expects($this->never())->method('postProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array()));
        $this->mockInterceptorInitializer->expects($this->never())
                                         ->method('getPostInterceptors');
        $this->mockRequest->expects($this->exactly(2))->method('isCancelled')->will($this->onConsecutiveCalls(false, true));
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo(404));
        $this->mockResponse->expects($this->once())->method('send');
        $this->sessionDataReplacement();
        $this->frontController->process();
    }

    /**
     * assure that processing ends when request is cancelled
     *
     * @test
     */
    public function processWithPostInterceptorCancellingRequest()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/')));
        $this->mockProcessor->expects($this->any())->method('forceSsl')->will($this->returnValue(false));
        $postInterceptor1 = $this->getMock('stubPostInterceptor');
        $postInterceptor1->expects($this->once())->method('postProcess');
        $postInterceptor2 = $this->getMock('stubPostInterceptor');
        $postInterceptor2->expects($this->never())->method('postProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array()));
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPostInterceptors')
                                         ->will($this->returnValue(array($postInterceptor1, $postInterceptor2)));
        $this->mockRequest->expects($this->exactly(3))
                          ->method('isCancelled')
                          ->will($this->onConsecutiveCalls(false, false, true));
        $this->mockProcessor->expects($this->any())->method('startup');
        $this->mockProcessor->expects($this->any())->method('process');
        $this->mockProcessor->expects($this->any())->method('cleanup');
        $this->mockResponse->expects($this->once())->method('send');
        $this->sessionDataReplacement();
        $this->frontController->process();
    }

    /**
     * assure that processing ends when request is cancelled
     *
     * @test
     */
    public function processWithoutCancellingRequest()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/')));
        $this->mockProcessor->expects($this->any())->method('forceSsl')->will($this->returnValue(false));
        $preInterceptor1 = $this->getMock('stubPreInterceptor');
        $preInterceptor1->expects($this->once())->method('preProcess');
        $preInterceptor2 = $this->getMock('stubPreInterceptor');
        $preInterceptor2->expects($this->once())->method('preProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array($preInterceptor1, $preInterceptor2)));
        $postInterceptor1 = $this->getMock('stubPostInterceptor');
        $postInterceptor1->expects($this->once())->method('postProcess');
        $postInterceptor2 = $this->getMock('stubPostInterceptor');
        $postInterceptor2->expects($this->once())->method('postProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPostInterceptors')
                                         ->will($this->returnValue(array($postInterceptor1, $postInterceptor2)));
        $this->mockRequest->expects($this->any())->method('isCancelled')->will($this->returnValue(false));
        $this->mockProcessor->expects($this->any())->method('startup');
        $this->mockProcessor->expects($this->any())->method('process');
        $this->mockProcessor->expects($this->any())->method('cleanup');
        $this->mockResponse->expects($this->once())->method('send');
        $this->sessionDataReplacement();
        $this->frontController->process();
    }

    /**
     * assure that processing ends when request is cancelled
     *
     * @test
     */
    public function processWithoutCancellingRequestWithoutResponseData()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/')));
        $this->mockProcessor->expects($this->any())->method('forceSsl')->will($this->returnValue(false));
        $preInterceptor1 = $this->getMock('stubPreInterceptor');
        $preInterceptor1->expects($this->once())->method('preProcess');
        $preInterceptor2 = $this->getMock('stubPreInterceptor');
        $preInterceptor2->expects($this->once())->method('preProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array($preInterceptor1, $preInterceptor2)));
        $postInterceptor1 = $this->getMock('stubPostInterceptor');
        $postInterceptor1->expects($this->once())->method('postProcess');
        $postInterceptor2 = $this->getMock('stubPostInterceptor');
        $postInterceptor2->expects($this->once())->method('postProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPostInterceptors')
                                         ->will($this->returnValue(array($postInterceptor1, $postInterceptor2)));
        $this->mockRequest->expects($this->any())->method('isCancelled')->will($this->returnValue(false));
        $this->mockProcessor->expects($this->any())->method('startup');
        $this->mockProcessor->expects($this->any())->method('process');
        $this->mockProcessor->expects($this->any())->method('cleanup');
        $this->mockResponse->expects($this->once())->method('send');
        $this->mockResponse->expects($this->once())
                           ->method('getBody')
                           ->will($this->returnValue(''));
        $this->mockResponse->expects($this->never())
                           ->method('replaceBody');
        $this->mockSession->expects($this->never())
                          ->method('getId');
        $this->mockSession->expects($this->never())
                          ->method('getName');
        $this->frontController->process();
    }

    /**
     * assure that processing ends when request is cancelled
     *
     * @test
     */
    public function forcesSslAndIsSsl()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/')));
        $this->mockProcessor->expects($this->any())->method('forceSsl')->will($this->returnValue(true));
        $this->mockProcessor->expects($this->any())->method('isSsl')->will($this->returnValue(true));
        $preInterceptor1 = $this->getMock('stubPreInterceptor');
        $preInterceptor1->expects($this->once())->method('preProcess');
        $preInterceptor2 = $this->getMock('stubPreInterceptor');
        $preInterceptor2->expects($this->once())->method('preProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array($preInterceptor1, $preInterceptor2)));
        $postInterceptor1 = $this->getMock('stubPostInterceptor');
        $postInterceptor1->expects($this->once())->method('postProcess');
        $postInterceptor2 = $this->getMock('stubPostInterceptor');
        $postInterceptor2->expects($this->once())->method('postProcess');
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPostInterceptors')
                                         ->will($this->returnValue(array($postInterceptor1, $postInterceptor2)));
        $this->mockRequest->expects($this->any())->method('isCancelled')->will($this->returnValue(false));
        $this->mockProcessor->expects($this->once())->method('startup');
        $this->mockProcessor->expects($this->once())->method('process');
        $this->mockProcessor->expects($this->once())->method('cleanup');
        $this->mockResponse->expects($this->once())->method('send');
        $this->sessionDataReplacement();
        $this->frontController->process();
    }

    /**
     * redirect to ssl if required
     *
     * @test
     */
    public function forcesSslButIsNotSsl()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/')));
        $this->mockProcessor->expects($this->any())->method('forceSsl')->will($this->returnValue(true));
        $this->mockProcessor->expects($this->any())->method('isSsl')->will($this->returnValue(false));
        $this->mockInterceptorInitializer->expects($this->once())
                                         ->method('getPreInterceptors')
                                         ->will($this->returnValue(array()));
        $this->mockInterceptorInitializer->expects($this->never())
                                         ->method('getPostInterceptors');
        $this->mockProcessor->expects($this->once())->method('startup');
        $this->mockProcessor->expects($this->never())->method('process');
        $this->mockProcessor->expects($this->never())->method('cleanup');
        $this->mockResponse->expects($this->once())->method('addHeader');
        $this->mockResponse->expects($this->once())->method('send');
        $this->frontController->process();
    }
}
?>