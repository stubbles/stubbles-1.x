<?php
/**
 * Tests for net::stubbles::webapp::auth::stubAuthProcessor.
 *
 * @package     stubbles
 * @subpackage  webapp_auth_test
 * @version     $Id: stubAuthProcessorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::auth::stubAuthProcessor');
/**
 * Tests for net::stubbles::webapp::auth::stubAuthProcessor.
 *
 * @package     stubbles
 * @subpackage  webapp_auth_test
 * @group       webapp
 * @group       webapp_auth
 */
class stubAuthProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  stubAuthProcessor
     */
    protected $authProcessor;
    /**
     * mocked decorated processor
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockProcessor;
    /**
     * mocked request instance
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
     * mocked auth handler instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockAuthHandler;
    /**
     * uri request to pass around
     *
     * @var  stubUriRequest
     */
    protected $uriRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockProcessor   = $this->getMock('stubProcessor');
        $this->mockRequest     = $this->getMock('stubRequest');
        $this->mockResponse    = $this->getMock('stubResponse');
        $this->mockAuthHandler = $this->getMock('stubAuthHandler');
        $this->authProcessor   = new stubAuthProcessor($this->mockProcessor,
                                                       $this->mockRequest,
                                                       $this->mockResponse,
                                                       $this->mockAuthHandler
                                 );
        $this->uriRequest      = new stubUriRequest('/');
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->authProcessor->getClass()
                                              ->getConstructor()
                                              ->hasAnnotation('Inject')
        );
    }

    /**
     * no route and no default role calls decorated processor
     *
     * @test
     */
    public function noRequiredRoleCallsDecoratedProcessor()
    {
        
        $this->mockProcessor->expects($this->once())
                            ->method('getRequiredRole')
                            ->with($this->equalTo(null))
                            ->will($this->returnValue(null));
        $this->mockAuthHandler->expects($this->once())
                              ->method('getDefaultRole')
                              ->will($this->returnValue(null));
        $this->mockAuthHandler->expects($this->never())
                              ->method('userHasRole');
        $this->mockProcessor->expects($this->once())
                            ->method('startup')
                            ->with($this->equalTo($this->uriRequest));
        $this->mockProcessor->expects($this->once())
                            ->method('process');
        $this->assertSame($this->authProcessor,
                          $this->authProcessor->startup($this->uriRequest)
                                              ->process()
        );
    }

    /**
     * no route but default role calls and user has default role decorated processor
     *
     * @test
     */
    public function defaultRoleAndUserHasDefaultRoleCallsDecoratedProcessor()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('getRequiredRole')
                            ->with($this->equalTo('guest'))
                            ->will($this->returnValue('guest'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('getDefaultRole')
                              ->will($this->returnValue('guest'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('userHasRole')
                              ->with($this->equalTo('guest'))
                              ->will($this->returnValue(true));
        $this->mockProcessor->expects($this->once())
                            ->method('startup')
                            ->with($this->equalTo($this->uriRequest));
        $this->mockProcessor->expects($this->once())
                            ->method('process');
        $this->assertSame($this->authProcessor,
                          $this->authProcessor->startup($this->uriRequest)
                                              ->process()
        );
    }

    /**
     * route with required role and no default role and user has required role calls decorated processor
     *
     * @test
     */
    public function requiredRoleAndNoDefaultRoleAndUserHasDefaultRoleCallsDecoratedProcessor()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('getRequiredRole')
                            ->with($this->equalTo(null))
                            ->will($this->returnValue('guest'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('getDefaultRole')
                              ->will($this->returnValue(null));
        $this->mockAuthHandler->expects($this->once())
                              ->method('userHasRole')
                              ->with($this->equalTo('guest'))
                              ->will($this->returnValue(true));
        $this->mockProcessor->expects($this->once())
                            ->method('startup')
                            ->with($this->equalTo($this->uriRequest));
        $this->mockProcessor->expects($this->once())
                            ->method('process');
        $this->assertSame($this->authProcessor,
                          $this->authProcessor->startup($this->uriRequest)
                                              ->process()
        );
    }

    /**
     * role required but user does not have role and no user set and role requires login sets location header
     *
     * @test
     */
    public function roleRequiredButUserDoesNotHaveRoleAndRoleRequiresLoginSetsLocationHeader()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('getRequiredRole')
                            ->with($this->equalTo('admin'))
                            ->will($this->returnValue('admin'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('getDefaultRole')
                              ->will($this->returnValue('admin'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('userHasRole')
                              ->with($this->equalTo('admin'))
                              ->will($this->returnValue(false));
        $this->mockAuthHandler->expects($this->once())
                              ->method('hasUser')
                              ->will($this->returnValue(false));
        $this->mockAuthHandler->expects($this->once())
                              ->method('requiresLogin')
                              ->with($this->equalTo('admin'))
                              ->will($this->returnValue(true));
        $this->mockAuthHandler->expects($this->once())
                              ->method('getLoginUrl')
                              ->will($this->returnValue('http://example.net/'));
        $this->mockProcessor->expects($this->once())
                            ->method('startup')
                            ->with($this->equalTo($this->uriRequest));
        $this->mockProcessor->expects($this->never())
                            ->method('process');
        $this->mockResponse->expects($this->once())
                           ->method('addHeader')
                           ->with($this->equalTo('Location'), $this->equalTo('http://example.net/'));
        $this->mockRequest->expects($this->once())
                          ->method('cancel');
        $this->assertSame($this->authProcessor,
                          $this->authProcessor->startup($this->uriRequest)
                                              ->process()
        );
    }

    /**
     * role required but user does not have role and no user set and role requires no login throws exception
     *
     * If a role is required but there is no user and the role requires no login
     *  - somewhat stupid. Most likely the auth handler is errounous then.
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function roleRequiredButUserDoesNotHaveRoleAndRoleRequiresNoLoginThrowsException()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('getRequiredRole')
                            ->with($this->equalTo('admin'))
                            ->will($this->returnValue('admin'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('getDefaultRole')
                              ->will($this->returnValue('admin'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('userHasRole')
                              ->with($this->equalTo('admin'))
                              ->will($this->returnValue(false));
        $this->mockAuthHandler->expects($this->exactly(2))
                              ->method('hasUser')
                              ->will($this->returnValue(false));
        $this->mockAuthHandler->expects($this->once())
                              ->method('requiresLogin')
                              ->with($this->equalTo('admin'))
                              ->will($this->returnValue(false));
        $this->mockProcessor->expects($this->once())
                            ->method('startup')
                            ->with($this->equalTo($this->uriRequest));
        $this->authProcessor->startup($this->uriRequest);
    }

    /**
     * role required but user does not have role and user set deny request
     *
     * @test
     * @expectedException  stubProcessorException
     */
    public function roleRequiredButUserDoesNotHaveRoleAndUserSetDenyRequest()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('getRequiredRole')
                            ->with($this->equalTo('admin'))
                            ->will($this->returnValue('admin'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('getDefaultRole')
                              ->will($this->returnValue('admin'));
        $this->mockAuthHandler->expects($this->once())
                              ->method('userHasRole')
                              ->with($this->equalTo('admin'))
                              ->will($this->returnValue(false));
        $this->mockAuthHandler->expects($this->exactly(2))
                              ->method('hasUser')
                              ->will($this->returnValue(true));
        $this->mockAuthHandler->expects($this->never())
                              ->method('requiresLogin');
        $this->mockAuthHandler->expects($this->never())
                              ->method('getLoginUrl');
        $this->mockProcessor->expects($this->once())
                            ->method('startup')
                            ->with($this->equalTo($this->uriRequest));
        $this->authProcessor->startup($this->uriRequest);
    }
}
?>