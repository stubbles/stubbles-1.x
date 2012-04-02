<?php
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantSettingPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubVariantSettingPreInterceptorTestCase.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubVariantSettingPreInterceptor');
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantSettingPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubVariantSettingPreInterceptorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubVariantSettingPreInterceptor
     */
    protected $variantSettingPreInterceptor;
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
     * set up test environment
     */
    public function setUp()
    {
        $this->mockVariantFactory           = $this->getMock('stubVariantFactory');
        $this->mockVariantCookieCreator     = $this->getMock('stubVariantsCookieCreator');
        $this->variantSettingPreInterceptor = $this->getMock('stubVariantSettingPreInterceptor',
                                                             array('canSelectVariant',
                                                                   'selectVariant'
                                                             ),
                                                             array($this->mockVariantFactory,
                                                                   $this->mockVariantCookieCreator
                                                             )
                                              );
        $this->mockRequest                  = $this->getMock('stubRequest');
        $this->mockSession                  = $this->getMock('stubSession');
        $this->mockResponse                 = $this->getMock('stubResponse');
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $class = new stubReflectionClass('stubVariantSettingPreInterceptor');
        $this->assertTrue($class->getConstructor()->hasAnnotation('Inject'));
    }

    /**
     * @test
     */
    public function doesNotSelectIfCanNotSelect()
    {
        $this->variantSettingPreInterceptor->expects($this->once())
                                           ->method('canSelectVariant')
                                           ->will($this->returnValue(false));
        $this->variantSettingPreInterceptor->expects($this->never())
                                           ->method('selectVariant');
        $this->mockSession->expects($this->never())
                          ->method('putValue');
        $this->mockResponse->expects($this->never())
                           ->method('addCookie');
        $this->variantSettingPreInterceptor->preProcess($this->mockRequest,
                                                        $this->mockSession,
                                                        $this->mockResponse
        );
    }

    /**
     * @test
     */
    public function doesNotStoreVariantIfNoneSelected()
    {
        $this->variantSettingPreInterceptor->expects($this->once())
                                           ->method('canSelectVariant')
                                           ->will($this->returnValue(true));
        $this->variantSettingPreInterceptor->expects($this->once())
                                           ->method('selectVariant')
                                           ->will($this->returnValue(null));
        $this->mockSession->expects($this->never())
                          ->method('putValue');
        $this->mockResponse->expects($this->never())
                           ->method('addCookie');
        $this->variantSettingPreInterceptor->preProcess($this->mockRequest,
                                                        $this->mockSession,
                                                        $this->mockResponse
        );
    }

    /**
     * @test
     */
    public function doesStoreVariantIfSelected()
    {
        $this->variantSettingPreInterceptor->expects($this->once())
                                           ->method('canSelectVariant')
                                           ->will($this->returnValue(true));
        $this->mockVariantFactory->expects($this->once())
                                 ->method('getVariantsMapName')
                                 ->will($this->returnValue('2010-12-13'));
        $mockVariant = $this->getMock('stubVariant');
        $mockVariant->expects($this->exactly(2))
                    ->method('getFullQualifiedName')
                    ->will($this->returnValue('foo:variantName'));
        $mockVariant->expects($this->once())
                    ->method('getAlias')
                    ->will($this->returnValue('alias'));
        $this->variantSettingPreInterceptor->expects($this->once())
                                           ->method('selectVariant')
                                           ->will($this->returnValue($mockVariant));
        $this->mockSession->expects($this->exactly(2))
                          ->method('putValue');
        $this->mockResponse->expects($this->exactly(2))
                           ->method('addCookie');
        $this->mockVariantCookieCreator->expects($this->once())
                                       ->method('createVariantCookie')
                                       ->with($this->equalTo('foo:variantName'))
                                       ->will($this->returnValue(stubCookie::create('variant', 'foo:variantName')));
        $this->mockVariantCookieCreator->expects($this->once())
                                       ->method('createMapCookie')
                                       ->with($this->equalTo('2010-12-13'))
                                       ->will($this->returnValue(stubCookie::create('variant', '2010-12-13')));
        $this->variantSettingPreInterceptor->preProcess($this->mockRequest,
                                                        $this->mockSession,
                                                        $this->mockResponse
        );
    }
}
?>