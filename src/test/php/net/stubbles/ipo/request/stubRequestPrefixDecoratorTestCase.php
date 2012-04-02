<?php
/**
 * Tests for net::stubbles::ipo::request::stubRequestPrefixDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubRequestPrefixDecoratorTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestPrefixDecorator');
/**
 * Tests for net::stubbles::ipo::request::stubRequestPrefixDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @group       ipo
 * @group       ipo_request
 */
class stubRequestPrefixDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRequestPrefixDecorator
     */
    protected $requestPrefixedParams;
    /**
     * instance to test
     *
     * @var  stubRequestPrefixDecorator
     */
    protected $requestPrefixedHeaders;
    /**
     * instance to test
     *
     * @var  stubRequestPrefixDecorator
     */
    protected $requestPrefixedCookies;
    /**
     * a mock to use for the checks
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest = $this->getMock('stubRequest');
        $this->requestPrefixedParams  = new stubRequestPrefixDecorator($this->mockRequest, 'test');
        $this->requestPrefixedHeaders = new stubRequestPrefixDecorator($this->mockRequest, 'test', stubRequest::SOURCE_HEADER);
        $this->requestPrefixedCookies = new stubRequestPrefixDecorator($this->mockRequest, 'test', stubRequest::SOURCE_COOKIE);
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function setPrefixReturnsItself()
    {
        $this->assertSame($this->requestPrefixedParams,
                          $this->requestPrefixedParams->setPrefix('foo')
        );
    }

    /**
     * @test
     */
    public function acceptsCookiesReliesOnDecoratedInstance()
    {
        $this->mockRequest->expects($this->exactly(2))
                          ->method('acceptsCookies')
                          ->will($this->onConsecutiveCalls(true, false));
        $this->assertTrue($this->requestPrefixedParams->acceptsCookies());
        $this->assertFalse($this->requestPrefixedParams->acceptsCookies());
    }

    /**
     * @test
     */
    public function cancelReliesOnDecoratedInstance()
    {
        $this->mockRequest->expects($this->once())
                          ->method('cancel');
        $this->mockRequest->expects($this->once())
                          ->method('isCancelled')
                          ->will($this->returnValue(true));
        $this->requestPrefixedParams->cancel();
        $this->assertTrue($this->requestPrefixedParams->isCancelled());
    }

    /**
     * @test
     */
    public function getMethodReliesOnDecoratedInstance()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getMethod')
                          ->will($this->returnValue('test'));
        $this->assertEquals('test', $this->requestPrefixedParams->getMethod());
    }

    /**
     * @test
     */
    public function getURIReliesOnDecoratedInstance()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getURI')
                          ->will($this->returnValue('test'));
        $this->assertEquals('test', $this->requestPrefixedParams->getURI());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getCompleteUriReliesOnDecoratedInstance()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getCompleteUri')
                          ->will($this->returnValue('test'));
        $this->assertEquals('test', $this->requestPrefixedParams->getCompleteUri());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasParamCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('test_foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('hasParam')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->onConsecutiveCalls(true, false));
         $this->assertTrue($this->requestPrefixedParams->hasParam('foo'));
         $this->assertFalse($this->requestPrefixedParams->hasParam('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasParamCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('hasParam')
                          ->with($this->equalTo('foo'))
                          ->will($this->onConsecutiveCalls(true, false));
         $this->assertTrue($this->requestPrefixedHeaders->hasParam('foo'));
         $this->assertFalse($this->requestPrefixedHeaders->hasParam('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasHeaderCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('test_foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('hasHeader')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->onConsecutiveCalls(true, false));
         $this->assertTrue($this->requestPrefixedHeaders->hasHeader('foo'));
         $this->assertFalse($this->requestPrefixedHeaders->hasHeader('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasHeaderCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('hasHeader')
                          ->with($this->equalTo('foo'))
                          ->will($this->onConsecutiveCalls(true, false));
         $this->assertTrue($this->requestPrefixedParams->hasHeader('foo'));
         $this->assertFalse($this->requestPrefixedParams->hasHeader('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasCookieCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('test_foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('hasCookie')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->onConsecutiveCalls(true, false));
         $this->assertTrue($this->requestPrefixedCookies->hasCookie('foo'));
         $this->assertFalse($this->requestPrefixedCookies->hasCookie('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasCookieCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('hasCookie')
                          ->with($this->equalTo('foo'))
                          ->will($this->onConsecutiveCalls(true, false));
         $this->assertTrue($this->requestPrefixedParams->hasCookie('foo'));
         $this->assertFalse($this->requestPrefixedParams->hasCookie('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateParamCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('test_foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('validateParam')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedParams->validateParam('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateParamCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('validateParam')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedHeaders->validateParam('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateHeaderCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('test_foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('validateHeader')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedHeaders->validateHeader('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateHeaderCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('validateHeader')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedParams->validateHeader('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateCookieCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('test_foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('validateCookie')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedCookies->validateCookie('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateCookieCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('foo', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('validateCookie')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedParams->validateCookie('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateBodyCallsMethodOfDecoratedRequestInstance()
    {
         $requestValue = new stubValidatingRequestValue('body', 'content');
         $this->mockRequest->expects($this->any())
                          ->method('validateBody')
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedParams->validateBody());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readParamCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                       $this->getMock('stubFilterFactory'),
                                                       'test_foo',
                                                       'content'
                         );
         $this->mockRequest->expects($this->any())
                          ->method('readParam')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedParams->readParam('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readParamCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                       $this->getMock('stubFilterFactory'),
                                                       'foo',
                                                       'content'
                         );
         $this->mockRequest->expects($this->any())
                          ->method('readParam')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedHeaders->readParam('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readHeaderCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                       $this->getMock('stubFilterFactory'),
                                                       'test_foo',
                                                       'content'
                         );
         $this->mockRequest->expects($this->any())
                          ->method('readHeader')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedHeaders->readHeader('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readHeaderCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                       $this->getMock('stubFilterFactory'),
                                                       'foo',
                                                       'content'
                         );
         $this->mockRequest->expects($this->any())
                          ->method('readHeader')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedParams->readHeader('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readCookieCallsMethodOfDecoratedPrefixedRequestInstance()
    {
         $requestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                       $this->getMock('stubFilterFactory'),
                                                       'test_foo',
                                                       'content'
                         );
         $this->mockRequest->expects($this->any())
                          ->method('readCookie')
                          ->with($this->equalTo('test_foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedCookies->readCookie('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readCookieCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
         $requestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                       $this->getMock('stubFilterFactory'),
                                                       'foo',
                                                       'content'
                         );
         $this->mockRequest->expects($this->any())
                          ->method('readCookie')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedParams->readCookie('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readBodyCallsMethodOfDecoratedRequestInstance()
    {
         $requestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                       $this->getMock('stubFilterFactory'),
                                                       'body',
                                                       'content'
                         );
         $this->mockRequest->expects($this->any())
                          ->method('readBody')
                          ->will($this->returnValue($requestValue));
         $this->assertSame($requestValue, $this->requestPrefixedParams->readBody());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getParamNamesCallsMethodOfDecoratedPrefixedRequestInstance()
    {
        $this->mockRequest->expects($this->any())
                          ->method('getParamNames')
                          ->will($this->returnValue(array('test_foo', 'bar_foo')));
        $this->assertEquals(array('foo'), $this->requestPrefixedParams->getParamNames());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getParamNamesCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
        $this->mockRequest->expects($this->any())
                          ->method('getParamNames')
                          ->will($this->returnValue(array('test_foo', 'bar_foo')));
        $this->assertEquals(array('test_foo', 'bar_foo'), $this->requestPrefixedCookies->getParamNames());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getHeaderNamesCallsMethodOfDecoratedPrefixedRequestInstance()
    {
        $this->mockRequest->expects($this->any())
                          ->method('getHeaderNames')
                          ->will($this->returnValue(array('test_foo', 'bar_foo')));
        $this->assertEquals(array('foo'), $this->requestPrefixedHeaders->getHeaderNames());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getHeaderNamesCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
        $this->mockRequest->expects($this->any())
                          ->method('getHeaderNames')
                          ->will($this->returnValue(array('test_foo', 'bar_foo')));
        $this->assertEquals(array('test_foo', 'bar_foo'), $this->requestPrefixedParams->getHeaderNames());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getCookieNamesCallsMethodOfDecoratedPrefixedRequestInstance()
    {
        $this->mockRequest->expects($this->any())
                          ->method('getCookieNames')
                          ->will($this->returnValue(array('test_foo', 'bar_foo')));
        $this->assertEquals(array('foo'), $this->requestPrefixedCookies->getCookieNames());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getCookieNamesCallsMethodOfDecoratedNonPrefixedRequestInstance()
    {
        $this->mockRequest->expects($this->any())
                          ->method('getCookieNames')
                          ->will($this->returnValue(array('test_foo', 'bar_foo')));
        $this->assertEquals(array('test_foo', 'bar_foo'), $this->requestPrefixedParams->getCookieNames());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function paramErrorsReturnsOriginalInstanceForNonPrefixedParams()
    {
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $this->mockRequest->expects($this->once())
                          ->method('paramErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $this->assertSame($mockRequestValueErrorCollection,
                          $this->requestPrefixedHeaders->paramErrors()
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function paramErrorsReturnsDecoratedInstanceForPrefixedParams()
    {
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $this->mockRequest->expects($this->once())
                          ->method('paramErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $decoratedRequestValueErrorCollection = $this->requestPrefixedParams->paramErrors();
        $this->assertInstanceOf('stubPrefixedRequestValueErrorCollection',
                          $decoratedRequestValueErrorCollection
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function headerErrorsReturnsOriginalInstanceForNonPrefixedParams()
    {
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $this->mockRequest->expects($this->once())
                          ->method('headerErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $this->assertSame($mockRequestValueErrorCollection,
                          $this->requestPrefixedParams->headerErrors()
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function headerErrorsReturnsDecoratedInstanceForPrefixedParams()
    {
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $this->mockRequest->expects($this->once())
                          ->method('headerErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $decoratedRequestValueErrorCollection = $this->requestPrefixedHeaders->headerErrors();
        $this->assertInstanceOf('stubPrefixedRequestValueErrorCollection',
                          $decoratedRequestValueErrorCollection
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function cookieErrorsReturnsOriginalInstanceForNonPrefixedParams()
    {
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $this->mockRequest->expects($this->once())
                          ->method('cookieErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $this->assertSame($mockRequestValueErrorCollection,
                          $this->requestPrefixedParams->cookieErrors()
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function cookieErrorsReturnsDecoratedInstanceForPrefixedParams()
    {
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $this->mockRequest->expects($this->once())
                          ->method('cookieErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $decoratedRequestValueErrorCollection = $this->requestPrefixedCookies->cookieErrors();
        $this->assertInstanceOf('stubPrefixedRequestValueErrorCollection',
                          $decoratedRequestValueErrorCollection
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function bodyErrorsPassesThruInstance()
    {
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $this->mockRequest->expects($this->once())
                          ->method('bodyErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $this->assertSame($mockRequestValueErrorCollection,
                          $this->requestPrefixedParams->bodyErrors()
        );
    }
}
?>