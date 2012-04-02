<?php
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubVariantsPreInterceptorProcessTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor',
                      'net::stubbles::webapp::variantmanager::stubVariantFactory',
                      'net::stubbles::webapp::variantmanager::types::stubVariant'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 */
class TestProcessstubVariantsPreInterceptor extends stubVariantsPreInterceptor
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
 * Test for net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubVariantsPreInterceptorProcessTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * the instance to test
     *
     * @var  TestProcessstubVariantsPreInterceptor
     */
    protected $variantPreInterceptor;
    /**
     * the mocked request
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * the mocked session
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
    /**
     * the mocked response
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * a mocked variant factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockVariantFactory;
    /**
     * a mocked variant cookie creator
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockVariantCookieCreator;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockSession              = $this->getMock('stubSession');
        $this->mockRequest              = $this->getMock('stubRequest');
        $this->mockResponse             = $this->getMock('stubResponse');
        $this->mockVariantFactory       = $this->getMock('stubVariantFactory');
        $this->variantPreInterceptor    = $this->getMock('TestProcessstubVariantsPreInterceptor',
                                                         array('getVariantFromCookie'),
                                                         array($this->mockVariantFactory,
                                                               $this->getMock('stubVariantsCookieCreator')
                                                         )
                                          );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $class = new stubReflectionClass('stubVariantsPreInterceptor');
        $this->assertTrue($class->getConstructor()->hasAnnotation('Inject'));
    }

    /**
     * @test
     */
    public function canNotSelectVariantIfVariantAlreadyInSession()
    {
        $this->mockSession->expects($this->once())->method('hasValue')->will($this->returnValue(true));
        $this->assertFalse($this->variantPreInterceptor->callCanSelectVariant($this->mockRequest, $this->mockSession, $this->mockResponse));
    }

    /**
     * @test
     */
    public function canSelectVariantIfNoVariantInSession()
    {
        $this->mockSession->expects($this->once())->method('hasValue')->will($this->returnValue(false));
        $this->assertTrue($this->variantPreInterceptor->callCanSelectVariant($this->mockRequest, $this->mockSession, $this->mockResponse));
    }

    /**

     * @test
     */
    public function noVariantCookieSetTriggersNewVariant()
    {
        $mockNewVariant = $this->getMock('stubVariant');
        $this->variantPreInterceptor->expects($this->once())
                                    ->method('getVariantFromCookie')
                                    ->will($this->returnValue(null));
        $this->mockVariantFactory->expects($this->once())
                                 ->method('shouldUsePersistence')
                                 ->will($this->returnValue(true));
        $this->mockVariantFactory->expects($this->once())
                                 ->method('getVariant')
                                 ->will($this->returnValue($mockNewVariant));
        $this->assertSame($mockNewVariant,
                          $this->variantPreInterceptor->callSelectVariant($this->mockRequest, $this->mockSession, $this->mockResponse)
        );
    }

    /**
     * @test
     */
    public function disabledPersistenceTriggersNewVariant()
    {
        $mockNewVariant = $this->getMock('stubVariant');
        $this->variantPreInterceptor->expects($this->never())
                                    ->method('getVariantFromCookie');
        $this->mockVariantFactory->expects($this->once())
                                 ->method('shouldUsePersistence')
                                 ->will($this->returnValue(false));
        $this->mockVariantFactory->expects($this->once())
                                 ->method('getVariant')
                                 ->will($this->returnValue($mockNewVariant));
        $this->assertSame($mockNewVariant,
                          $this->variantPreInterceptor->callSelectVariant($this->mockRequest, $this->mockSession, $this->mockResponse)
        );
    }

    /**
     * @test
     */
    public function variantCookieSet()
    {
        $mockCookieVariant = $this->getMock('stubVariant');
        $this->variantPreInterceptor->expects($this->once())
                                    ->method('getVariantFromCookie')
                                    ->will($this->returnValue($mockCookieVariant));
        $this->mockVariantFactory->expects($this->once())
                                 ->method('shouldUsePersistence')
                                 ->will($this->returnValue(true));
        $this->mockVariantFactory->expects($this->never())
                                 ->method('getVariant');
        $this->assertSame($mockCookieVariant,
                          $this->variantPreInterceptor->callSelectVariant($this->mockRequest, $this->mockSession, $this->mockResponse)
        );
    }
}
?>