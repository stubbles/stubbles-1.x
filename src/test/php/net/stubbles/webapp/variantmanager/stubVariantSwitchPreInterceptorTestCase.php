<?php
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantSwitchPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubVariantSwitchPreInterceptorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubVariantSwitchPreInterceptor',
                      'net::stubbles::webapp::variantmanager::types::stubVariant'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 */
class TeststubVariantSwitchPreInterceptor extends stubVariantSwitchPreInterceptor
{
    /**
     * checks if there is enough data to select a variant
     *
     * @param   stubRequest   $request   access to request data
     * @param   stubSession   $session   access to session data
     * @param   stubResponse  $response  access to response data
     * @return  bool
     */
    public function callCanSelectVariant(stubRequest $request, stubSession $session, stubResponse $response)
    {
        return $this->canSelectVariant($request, $session, $response);
    }

    /**
     * selects variant based on request and session data
     *
     * @param   stubRequest   $request   access to request data
     * @param   stubSession   $session   access to session data
     * @param   stubResponse  $response  access to response data
     * @return  stubVariant
     */
    public function callSelectVariant(stubRequest $request, stubSession $session, stubResponse $response)
    {
        return $this->selectVariant($request, $session, $response);
    }
}
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantSwitchPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubVariantSwitchPreInterceptorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubVariantSwitchPreInterceptor
     */
    protected $variantSwitchPreInterceptor;
    /**
     * a mocked variant factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockVariantFactory;
    /**
     * mocked runtime mode
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockMode;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockVariantFactory          = $this->getMock('stubVariantFactory');
        $this->variantSwitchPreInterceptor = new TeststubVariantSwitchPreInterceptor($this->mockVariantFactory,
                                                                                     $this->getMock('stubVariantsCookieCreator')
                                              );
        $this->mockMode                    = $this->getMock('stubMode');
        $this->mockRequest                 = $this->getMock('stubRequest');
        $this->mockSession                 = $this->getMock('stubSession');
        $this->mockResponse                = $this->getMock('stubResponse');
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetModeMethod()
    {
        $setModeMethod = $this->variantSwitchPreInterceptor->getClass()->getMethod('setMode');
        $this->assertTrue($setModeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setModeMethod->getAnnotation('Inject')->isOptional());
    }

    /**
     * @test
     */
    public function canNotSelectVariantIfNoModeSet()
    {
        $this->assertFalse($this->variantSwitchPreInterceptor->callCanSelectVariant($this->mockRequest,
                                                                                    $this->mockSession,
                                                                                    $this->mockResponse
                                                               )
        );
    }

    /**
     * @test
     */
    public function canNotSelectVariantIfModeNotStageOrDev()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $this->variantSwitchPreInterceptor->setMode($this->mockMode);
        $this->assertFalse($this->variantSwitchPreInterceptor->callCanSelectVariant($this->mockRequest,
                                                                                    $this->mockSession,
                                                                                    $this->mockResponse
                                                               )
        );
    }

    /**
     * @test
     */
    public function canNotSelectVariantIfRequestParamNotSet()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('STAGE'));
        $this->variantSwitchPreInterceptor->setMode($this->mockMode);
        $this->mockRequest->expects(($this->once()))
                          ->method('hasParam')
                          ->will($this->returnValue(false));
        $this->assertFalse($this->variantSwitchPreInterceptor->callCanSelectVariant($this->mockRequest,
                                                                                    $this->mockSession,
                                                                                    $this->mockResponse
                                                               )
        );
    }

    /**
     * @test
     */
    public function canSelectVariantIfRequestParamSetAndCorrectMode()
    {
        $this->mockMode->expects($this->any())
                       ->method('name')
                       ->will($this->returnValue('DEV'));
        $this->variantSwitchPreInterceptor->setMode($this->mockMode);
        $this->mockRequest->expects(($this->once()))
                          ->method('hasParam')
                          ->will($this->returnValue(true));
        $this->assertTrue($this->variantSwitchPreInterceptor->callCanSelectVariant($this->mockRequest,
                                                                                   $this->mockSession,
                                                                                   $this->mockResponse
                                                              )
        );
    }

    /**
     * @test
     */
    public function selectVariantReturnsNullIfRequestVariantDoesNotExist()
    {
        $this->mockRequest->expects(($this->once()))
                          ->method('readParam')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('__variant', 'doesNotExist')));
        $this->mockVariantFactory->expects($this->once())
                                 ->method('getVariantNames')
                                 ->will($this->returnValue(array('lead:foo', 'lead:bar')));
        $this->mockVariantFactory->expects($this->never())
                                 ->method('getVariantByName');
        $this->assertNull($this->variantSwitchPreInterceptor->callSelectVariant($this->mockRequest,
                                                                                $this->mockSession,
                                                                                $this->mockResponse
                                                              )
        );
    }

    /**
     * @test
     */
    public function selectVariantReturnsSelectedVariant()
    {
        $this->mockRequest->expects(($this->once()))
                          ->method('readParam')
                          ->will($this->returnValue(new stubMockFilteringRequestValue('__variant', 'lead:bar')));
        $this->mockVariantFactory->expects($this->once())
                                 ->method('getVariantNames')
                                 ->will($this->returnValue(array('lead:foo', 'lead:bar')));
        $mockVariant = $this->getMock('stubVariant');
        $this->mockVariantFactory->expects($this->once())
                                 ->method('getVariantByName')
                                 ->with($this->equalTo('lead:bar'))
                                 ->will($this->returnValue($mockVariant));
        $this->assertSame($mockVariant,
                          $this->variantSwitchPreInterceptor->callSelectVariant($this->mockRequest,
                                                                                $this->mockSession,
                                                                                $this->mockResponse
                                                              )
        );
    }
}
?>