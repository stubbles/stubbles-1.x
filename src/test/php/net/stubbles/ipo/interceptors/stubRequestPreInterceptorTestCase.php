<?php
/**
 * Tests for net::stubbles::ipo::interceptors::stubRequestPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors_test
 * @version     $Id: stubRequestPreInterceptorTestCase.php 3249 2011-11-30 18:04:16Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubRequestPreInterceptor');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors_test
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class DecoratedPreInterceptor extends stubBaseObject implements stubPreInterceptor
{
    /**
     * counter for calls to the pre interceptor
     *
     * @var  int
     */
    public static $called = 0;
    /**
     * request instance
     *
     * @var  stubRequest
     */
    public static $request;
    /**
     * session instance
     *
     * @var  stubSession
     */
    public static $session;
    /**
     * response instance
     *
     * @var  stubResponse
     */
    public static $response;

    /**
     * does the preprocessing stuff
     *
     * @param  stubRequest   $request   access to request data
     * @param  stubSession   $session   access to session data
     * @param  stubResponse  $response  access to response data
     */
    public function preProcess(stubRequest $request, stubSession $session, stubResponse $response)
    {
        self::$called++;
        self::$request  = $request;
        self::$session  = $session;
        self::$response = $response;
    }
}
/**
 * Tests for net::stubbles::ipo::interceptors::stubRequestPreInterceptor.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors_test
 * @group       ipo
 * @group       ipo_interceptors
 */
class stubRequestPreInterceptorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRequestPreInterceptor
     */
    protected $requestPreInterceptor;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked session instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
    /**
     * mocked response instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->requestPreInterceptor = new stubRequestPreInterceptor('DecoratedPreInterceptor', 'param');
        $this->mockRequest           = $this->getMock('stubRequest');
        $this->mockSession           = $this->getMock('stubSession');
        $this->mockResponse          = $this->getMock('stubResponse');
    }

    /**
     * properties should be set and correctly returned
     *
     * @test
     */
    public function propertiesSet()
    {
        $this->assertEquals('DecoratedPreInterceptor', $this->requestPreInterceptor->getDecoratedPreInterceptor());
        $this->assertEquals('param', $this->requestPreInterceptor->getRequestParamName());
    }

    /**
     * do not call decorated interceptor if param is not set
     *
     * @test
     */
    public function paramNotSet()
    {
        $this->mockRequest->expects($this->once())->method('hasParam')->will($this->returnValue(false));
        $this->requestPreInterceptor->preProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
        $this->assertEquals(0, DecoratedPreInterceptor::$called);
        $this->assertNull(DecoratedPreInterceptor::$request);
        $this->assertNull(DecoratedPreInterceptor::$session);
        $this->assertNull(DecoratedPreInterceptor::$response);
    }

    /**
     * call decorated interceptor if param is set
     *
     * @test
     */
    public function paramSet()
    {
        $this->mockRequest->expects($this->once())->method('hasParam')->will($this->returnValue(true));
        $this->requestPreInterceptor->preProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
        $this->assertEquals(1, DecoratedPreInterceptor::$called);
        $this->assertSame($this->mockRequest, DecoratedPreInterceptor::$request);
        $this->assertSame($this->mockSession, DecoratedPreInterceptor::$session);
        $this->assertSame($this->mockResponse, DecoratedPreInterceptor::$response);
    }
}
?>