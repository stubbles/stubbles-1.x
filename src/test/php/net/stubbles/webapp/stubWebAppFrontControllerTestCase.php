<?php
/**
 * Test for net::stubbles::webapp::stubWebAppFrontController.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::stubMockFilteringRequestValue',
                      'net::stubbles::webapp::stubWebAppFrontController'
);
/**
 * Test for net::stubbles::webapp::stubWebAppFrontController.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 * @group       webapp
 */
class stubWebAppFrontControllerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubWebAppFrontController
     */
    protected $webAppFrontController;
    /**
     * mocked contains request data
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked session container
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
    /**
     * mocked response container
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;
    /**
     * mocked uri configuration
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockUriConfig;
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
        $this->mockRequest           = $this->getMock('stubRequest');
        $this->mockSession           = $this->getMock('stubSession');
        $this->mockResponse          = $this->getMock('stubResponse');
        $this->mockInjector          = $this->getMock('stubInjector');
        $this->mockUriConfig         = $this->getMock('stubUriConfiguration', array(), array('example'));
        $this->webAppFrontController = new stubWebAppFrontController($this->mockRequest,
                                                                     $this->mockSession,
                                                                     $this->mockResponse,
                                                                     $this->mockInjector,
                                                                     $this->mockUriConfig
                                       );
        $this->mockProcessor         = $this->getMock('stubProcessor');
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
     * @test
     */
    public function annotationPresentOnConstructor()
    {
        $this->assertTrue($this->webAppFrontController->getClass()
                                                      ->getConstructor()
                                                      ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function processWithAlreadyCancelledRequestNeverCallsInterceptorsOrProcessor()
    {
        $this->mockRequest->expects($this->once())
                          ->method('isCancelled')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->never())
                          ->method('readHeader');
        $this->mockUriConfig->expects($this->never())
                            ->method('getPreInterceptors');
        $this->mockUriConfig->expects($this->never())
                            ->method('getProcessorName');
        $this->mockUriConfig->expects($this->never())
                            ->method('getPostInterceptors');
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->sessionDataReplacement();
        $this->webAppFrontController->process();
    }

    /**
     * @test
     */
    public function processStopsIfPreInterceptorCancelsRequest()
    {
        $this->mockRequest->expects($this->exactly(3))
                          ->method('isCancelled')
                          ->will($this->onConsecutiveCalls(false, false, true));
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/xml/Home')));
        $this->mockUriConfig->expects($this->once())
                            ->method('getPreInterceptors')
                            ->will($this->returnValue(array('my::PreInterceptor',
                                                            'my::OtherPreInterceptor',
                                                            'other::PreInterceptor'
                                                      )
                                   )
                              );
        $this->mockUriConfig->expects($this->never())
                            ->method('getProcessorName');
        $this->mockUriConfig->expects($this->never())
                            ->method('getPostInterceptors');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('my::OtherPreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockResponse->expects($this->once())
                           ->method('send');
        $this->sessionDataReplacement();
        $this->webAppFrontController->process();
    }

    /**
     * @test
     */
    public function processStopsIfProcessorCancelsRequest()
    {
        $this->mockRequest->expects($this->exactly(4))
                          ->method('isCancelled')
                          ->will($this->onConsecutiveCalls(false, false, false, true));
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/xml/Home')));
        $this->mockUriConfig->expects($this->once())
                            ->method('getPreInterceptors')
                            ->will($this->returnValue(array('my::PreInterceptor',
                                                            'other::PreInterceptor'
                                                      )
                                   )
                              );
        $this->mockUriConfig->expects($this->once())
                            ->method('getProcessorName')
                            ->will($this->returnValue('example'));
        $this->mockUriConfig->expects($this->never())
                            ->method('getPostInterceptors');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('other::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(2))
                           ->method('getInstance')
                           ->with($this->equalTo('stubProcessor'), $this->equalTo('example'))
                           ->will($this->returnValue($this->mockProcessor));
        $this->mockResponse->expects($this->once())
                           ->method('send');
        $this->sessionDataReplacement();
        $this->webAppFrontController->process();
    }

    /**
     * @test
     */
    public function processStopsIfProcessorThrowsException()
    {
        $this->mockRequest->expects($this->exactly(4))
                          ->method('isCancelled')
                          ->will($this->onConsecutiveCalls(false, false, false, true));
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/xml/Home')));
        $this->mockUriConfig->expects($this->once())
                            ->method('getPreInterceptors')
                            ->will($this->returnValue(array('my::PreInterceptor',
                                                            'other::PreInterceptor'
                                                      )
                                   )
                              );
        $this->mockUriConfig->expects($this->once())
                            ->method('getProcessorName')
                            ->will($this->returnValue('example'));
        $this->mockUriConfig->expects($this->never())
                            ->method('getPostInterceptors');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('other::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(2))
                           ->method('getInstance')
                           ->with($this->equalTo('stubProcessor'), $this->equalTo('example'))
                           ->will($this->returnValue($this->mockProcessor));
        $this->mockProcessor->expects($this->once())
                            ->method('startup')
                            ->will($this->throwException(new stubProcessorException(500, 'Error during processing.')));
        $this->mockRequest->expects($this->once())
                          ->method('cancel');
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo(500));
        $this->mockResponse->expects($this->once())
                           ->method('send');
        $this->sessionDataReplacement();
        $this->webAppFrontController->process();
    }

    /**
     * @test
     */
    public function processStopsIfProcessorRequiresSslButIsNotSslAndRedirectsToSslVersion()
    {
        $this->mockRequest->expects($this->exactly(3))
                          ->method('isCancelled')
                          ->will($this->onConsecutiveCalls(false, false, false));
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/xml/Home')));
        $this->mockUriConfig->expects($this->once())
                            ->method('getPreInterceptors')
                            ->will($this->returnValue(array('my::PreInterceptor',
                                                            'other::PreInterceptor'
                                                      )
                                   )
                              );
        $this->mockUriConfig->expects($this->once())
                            ->method('getProcessorName')
                            ->will($this->returnValue('example'));
        $this->mockUriConfig->expects($this->never())
                            ->method('getPostInterceptors');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('other::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(2))
                           ->method('getInstance')
                           ->with($this->equalTo('stubProcessor'), $this->equalTo('example'))
                           ->will($this->returnValue($this->mockProcessor));
        $this->mockProcessor->expects($this->once())
                            ->method('startup');
        $this->mockProcessor->expects($this->once())
                            ->method('forceSsl')
                            ->will($this->returnValue(true));
        $this->mockProcessor->expects($this->once())
                            ->method('isSsl')
                            ->will($this->returnValue(false));
        $this->mockRequest->expects($this->once())
                          ->method('cancel');
        $this->mockRequest->expects($this->once())
                          ->method('getURI')
                          ->will($this->returnValue('www.example.com/xml/Home'));
        $this->mockResponse->expects($this->once())
                           ->method('redirect')
                           ->with($this->equalTo('https://www.example.com/xml/Home'));
        $this->mockResponse->expects($this->once())
                           ->method('send');
        $this->sessionDataReplacement();
        $this->webAppFrontController->process();
    }

    /**
     * @test
     */
    public function processStopsIfPostInterceptorCancelsRequest()
    {
        $this->mockRequest->expects($this->exactly(5))
                          ->method('isCancelled')
                          ->will($this->onConsecutiveCalls(false, false, false, false, true));
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/xml/Home')));
        $this->mockUriConfig->expects($this->once())
                            ->method('getPreInterceptors')
                            ->will($this->returnValue(array('my::PreInterceptor',
                                                            'other::PreInterceptor'
                                                      )
                                   )
                              );
        $this->mockUriConfig->expects($this->once())
                            ->method('getProcessorName')
                            ->will($this->returnValue('example'));
        $this->mockUriConfig->expects($this->once())
                            ->method('getPostInterceptors')
                            ->will($this->returnValue(array('my::PostInterceptor',
                                                            'other::PostInterceptor'
                                                      )
                                   )
                              );
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('other::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(2))
                           ->method('getInstance')
                           ->with($this->equalTo('stubProcessor'), $this->equalTo('example'))
                           ->will($this->returnValue($this->mockProcessor));
        $this->mockInjector->expects($this->at(3))
                           ->method('getInstance')
                           ->with($this->equalTo('my::PostInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPostInterceptor')));
        $this->mockProcessor->expects($this->once())
                            ->method('startup');
        $this->mockProcessor->expects($this->once())
                            ->method('process');
        $this->mockProcessor->expects($this->once())
                            ->method('cleanup');
        $this->mockResponse->expects($this->once())
                           ->method('send');
        $this->sessionDataReplacement();
        $this->webAppFrontController->process();
    }

    /**
     * @test
     */
    public function processNeversStopsIfRequestNotCancelled()
    {
        $this->mockRequest->expects($this->any())
                          ->method('isCancelled')
                          ->will($this->returnValue(false));
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('REQUEST_URI', '/xml/Home')));
        $this->mockUriConfig->expects($this->once())
                            ->method('getPreInterceptors')
                            ->will($this->returnValue(array('my::PreInterceptor',
                                                            'other::PreInterceptor'
                                                      )
                                   )
                              );
        $this->mockUriConfig->expects($this->once())
                            ->method('getProcessorName')
                            ->will($this->returnValue('example'));
        $this->mockUriConfig->expects($this->once())
                            ->method('getPostInterceptors')
                            ->will($this->returnValue(array('my::PostInterceptor',
                                                            'other::PostInterceptor'
                                                      )
                                   )
                              );
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('other::PreInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPreInterceptor')));
        $this->mockInjector->expects($this->at(2))
                           ->method('getInstance')
                           ->with($this->equalTo('stubProcessor'), $this->equalTo('example'))
                           ->will($this->returnValue($this->mockProcessor));
        $this->mockInjector->expects($this->at(3))
                           ->method('getInstance')
                           ->with($this->equalTo('my::PostInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPostInterceptor')));
        $this->mockInjector->expects($this->at(4))
                           ->method('getInstance')
                           ->with($this->equalTo('other::PostInterceptor'))
                           ->will($this->returnValue($this->getMock('stubPostInterceptor')));
        $this->mockProcessor->expects($this->once())
                            ->method('startup');
        $this->mockProcessor->expects($this->once())
                            ->method('process');
        $this->mockProcessor->expects($this->once())
                            ->method('cleanup');
        $this->mockResponse->expects($this->once())
                           ->method('send');
        $this->sessionDataReplacement();
        $this->webAppFrontController->process();
    }
}
?>