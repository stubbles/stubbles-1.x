<?php
/**
 * Tests for net::stubbles::websites::processors::auth::stubAuthProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors_auth_test
 * @version     $Id: stubAuthProcessorResolverTestCase.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::auth::stubAuthProcessorResolver');
/**
 * Tests for net::stubbles::websites::processors::auth::stubAuthProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors_auth_test
 * @deprecated
 * @group       websites
 * @group       websites_processors
 * @group       websites_processors_auth
 */
class stubAuthProcessorResolverTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  stubAuthProcessorResolver
     */
    protected $authProcessorResolver;
    /**
     * mocked processor resolver
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockProcessorResolver;
    /**
     * mocked auth handler instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockAuthHandler;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockProcessorResolver = $this->getMock('stubProcessorResolver');
        $binder                      = new stubBinder();
        $this->mockAuthHandler       = $this->getMock('stubAuthHandler');
        $binder->bind('stubAuthHandler')
               ->toInstance($this->mockAuthHandler);
        $this->authProcessorResolver   = new stubAuthProcessorResolver($this->mockProcessorResolver,
                                                                       $binder->getInjector()
                                         );
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $constructor = $this->authProcessorResolver->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        
        $params = $constructor->getParameters();
        $this->assertTrue($params[0]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.websites.processor.finalResolver', $params[0]->getAnnotation('Named')->getName());
    }

    /**
     * interceptor descriptor for original processor isReturned
     *
     * @test
     */
    public function interceptorDescriptorFromParentResolverIsReturned()
    {
        $mockRequest = $this->getMock('stubRequest');
        $this->mockProcessorResolver->expects($this->once())
                                    ->method('getInterceptorDescriptor')
                                    ->with($this->equalTo($mockRequest))
                                    ->will($this->returnValue('interceptors'));
        $this->assertEquals('interceptors', $this->authProcessorResolver->getInterceptorDescriptor($mockRequest));
    }

    /**
     * resolve() returns auth processor
     *
     * @test
     */
    public function resolveReturnsAuthProcessor()
    {
        $mockRequest   = $this->getMock('stubRequest');
        $mockSession   = $this->getMock('stubSession');
        $mockResponse  = $this->getMock('stubResponse');
        $mockProcessor = $this->getMock('stubProcessor');
        $this->mockProcessorResolver->expects($this->once())
                                    ->method('resolve')
                                    ->with($this->equalTo($mockRequest), $this->equalTo($mockSession), $this->equalTo($mockResponse))
                                    ->will($this->returnValue($mockProcessor));
        $authProcessor = $this->authProcessorResolver->resolve($mockRequest, $mockSession, $mockResponse);
        $this->assertInstanceOf('stubAuthProcessor', $authProcessor);
    }
}
?>