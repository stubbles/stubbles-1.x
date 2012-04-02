<?php
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubVariantsPreInterceptorCookieVariantTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor',
                      'net::stubbles::webapp::variantmanager::stubVariantFactory',
                      'net::stubbles::webapp::variantmanager::types::stubVariant'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 */
class TestVariantsPreInterceptor extends stubVariantsPreInterceptor
{
    /**
     * returns the cookie variant
     *
     * @param   stubRequest  $request
     * @param   stubSession  $session
     * @return  stubVariant
     */
    public function getCookieVariant(stubRequest $request, stubSession $session)
    {
        return $this->getVariantFromCookie($request, $session);
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
class stubVariantsPreInterceptorCookieVariantTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * the instance to test
     *
     * @var  stubVariantsPreInterceptor
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
     * a mocked variant factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockVariantFactory;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockSession           = $this->getMock('stubSession');
        $this->mockRequest           = $this->getMock('stubRequest');
        $this->mockVariantFactory    = $this->getMock('stubVariantFactory');
        $this->mockVariantFactory->expects($this->any())
                                 ->method('getVariantNames')
                                 ->will($this->returnValue(array('fooVariant')));
        $this->mockVariantFactory->expects($this->any())
                                 ->method('getVariantsMapName')
                                 ->will($this->returnValue('2010-12-10'));
        $mockVariantCookieCreator = $this->getMock('stubVariantsCookieCreator');
        $mockVariantCookieCreator->expects($this->any())
                                       ->method('getCookieMapName')
                                       ->will($this->returnValue('variant.configname'));
        $mockVariantCookieCreator->expects($this->any())
                                       ->method('getCookieName')
                                       ->will($this->returnValue('variant'));
        $this->variantPreInterceptor = new TestVariantsPreInterceptor($this->mockVariantFactory,
                                                                      $mockVariantCookieCreator
                                       );
    }

    /**
     * @test
     */
    public function noCookieWillReturnWithoutVariant()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(false));
        $this->assertNull($this->variantPreInterceptor->getCookieVariant($this->mockRequest,
                                                                         $this->mockSession,
                                                                         $this->mockVariantFactory
                          )
        );
    }

    /**
     * @test
     */
    public function missingMapNameCookieWillReturnWithoutVariant()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(true));
         $this->mockRequest->expects($this->once())
                          ->method('validateCookie')
                          ->will($this->returnValue(new stubValidatingRequestValue('variant.configname',
                                                                                   null
                                                    )
                                 )
                            );
        $this->assertNull($this->variantPreInterceptor->getCookieVariant($this->mockRequest,
                                                                         $this->mockSession,
                                                                         $this->mockVariantFactory
                          )
        );
    }

    /**
     * @test
     */
    public function invalidMapNameCookieWillReturnWithoutVariant()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(true));
         $this->mockRequest->expects($this->once())
                          ->method('validateCookie')
                          ->will($this->returnValue(new stubValidatingRequestValue('variant.configname',
                                                                                   '2010-11-11'
                                                    )
                                 )
                            );
        $this->assertNull($this->variantPreInterceptor->getCookieVariant($this->mockRequest,
                                                                         $this->mockSession,
                                                                         $this->mockVariantFactory
                          )
        );
    }

    /**
     * @test
     */
    public function invalidCookieWillNotBeUsed()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('validateCookie')
                          ->will($this->returnValue(new stubValidatingRequestValue('variant.configname',
                                                                                   '2010-12-10'
                                                    )
                                 )
                            );
        $this->mockRequest->expects($this->once())
                          ->method('readCookie')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'variant',
                                                                                  null
                                                    )
                                 )
                            );
        $this->mockVariantFactory->expects($this->once())
                                 ->method('getVariantNames')
                                 ->will($this->returnValue(array()));
        $this->assertNull($this->variantPreInterceptor->getCookieVariant($this->mockRequest,
                                                                         $this->mockSession,
                                                                         $this->mockVariantFactory
                          )
        );
    }

    /**
     * @test
     */
    public function validCookieWithInvalidVariantWillNotBeUsed()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('validateCookie')
                          ->will($this->returnValue(new stubValidatingRequestValue('variant.configname',
                                                                                   '2010-12-10'
                                                    )
                                 )
                            );
        $this->mockRequest->expects($this->once())
                          ->method('readCookie')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'variant',
                                                                                  'fooVariant'
                                                    )
                                 )
                            );
        $this->mockVariantFactory->expects($this->once())->method('getVariantNames')->will($this->returnValue(array('fooVariant')));
        $this->mockVariantFactory->expects($this->once())->method('isVariantValid')->will($this->returnValue(false));
        $this->assertNull($this->variantPreInterceptor->getCookieVariant($this->mockRequest, $this->mockSession, $this->mockVariantFactory));
    }

    /**
     * @test
     */
    public function validCookieWithoutEnforcingVariantWillUseVariantFromCookie()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('validateCookie')
                          ->will($this->returnValue(new stubValidatingRequestValue('variant.configname',
                                                                                   '2010-12-10'
                                                    )
                                 )
                            );
        $this->mockRequest->expects($this->once())
                          ->method('readCookie')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'variant',
                                                                                  'fooVariant'
                                                    )
                                 )
                            );
        $this->mockVariantFactory->expects($this->once())->method('getVariantNames')->will($this->returnValue(array()));
        $this->mockVariantFactory->expects($this->once())->method('isVariantValid')->will($this->returnValue(true));
        $fooVariant = $this->getMock('stubVariant');
        $fooVariant->expects($this->any())->method('getName')->will($this->returnValue('fooVariant'));
        $this->mockVariantFactory->expects($this->once())->method('getVariantByName')->will($this->returnValue($fooVariant));
        $this->mockVariantFactory->expects($this->once())->method('getEnforcingVariant')->will($this->returnValue(null));
        $resultVariant = $this->variantPreInterceptor->getCookieVariant($this->mockRequest, $this->mockSession, $this->mockVariantFactory);
        $this->assertSame($fooVariant, $resultVariant);
    }

    /**
     * @test
     */
    public function validCookieWithValidEnforcingVariantWillUseEnforcingVariant()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('validateCookie')
                          ->will($this->returnValue(new stubValidatingRequestValue('variant.configname',
                                                                                   '2010-12-10'
                                                    )
                                 )
                            );
        $this->mockRequest->expects($this->once())
                          ->method('readCookie')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'variant',
                                                                                  'fooVariant'
                                                    )
                                 )
                            );
        $this->mockVariantFactory->expects($this->once())->method('getVariantNames')->will($this->returnValue(array()));
        $this->mockVariantFactory->expects($this->once())->method('isVariantValid')->will($this->returnValue(true));
        $fooVariant = $this->getMock('stubVariant');
        $fooVariant->expects($this->any())->method('getName')->will($this->returnValue('fooVariant'));
        $fooVariant->expects($this->any())->method('getFullQualifiedName')->will($this->returnValue('fooVariant'));
        $this->mockVariantFactory->expects($this->once())->method('getVariantByName')->will($this->returnValue($fooVariant));
        $barVariant = $this->getMock('stubVariant');
        $barVariant->expects($this->any())->method('getName')->will($this->returnValue('barVariant'));
        $barVariant->expects($this->any())->method('getFullQualifiedName')->will($this->returnValue('barVariant'));
        $this->mockVariantFactory->expects($this->once())->method('getEnforcingVariant')->will($this->returnValue($barVariant));
        $resultVariant = $this->variantPreInterceptor->getCookieVariant($this->mockRequest, $this->mockSession, $this->mockVariantFactory);
        $this->assertSame($barVariant, $resultVariant);
    }

    /**
     * @test
     */
    public function validCookieWithValidEnforcingVariantAndCookieVariantIsSubvariantOfEnforcingVariant()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('validateCookie')
                          ->will($this->returnValue(new stubValidatingRequestValue('variant.configname',
                                                                                   '2010-12-10'
                                                    )
                                 )
                            );
        $this->mockRequest->expects($this->once())
                          ->method('readCookie')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'variant',
                                                                                  'fooVariant'
                                                    )
                                 )
                            );
        $this->mockVariantFactory->expects($this->once())->method('getVariantNames')->will($this->returnValue(array()));
        $this->mockVariantFactory->expects($this->once())->method('isVariantValid')->will($this->returnValue(true));
        $fooVariant = $this->getMock('stubVariant');
        $fooVariant->expects($this->any())->method('getName')->will($this->returnValue('fooVariant'));
        $fooVariant->expects($this->any())->method('getFullQualifiedName')->will($this->returnValue('fooVariant'));
        $this->mockVariantFactory->expects($this->once())->method('getVariantByName')->will($this->returnValue($fooVariant));
        $barVariant = $this->getMock('stubVariant');
        $barVariant->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $barVariant->expects($this->any())->method('getFullQualifiedName')->will($this->returnValue('foo'));
        $this->mockVariantFactory->expects($this->once())->method('getEnforcingVariant')->will($this->returnValue($barVariant));
        $resultVariant = $this->variantPreInterceptor->getCookieVariant($this->mockRequest, $this->mockSession, $this->mockVariantFactory);
        $this->assertSame($fooVariant, $resultVariant);
    }
}
?>