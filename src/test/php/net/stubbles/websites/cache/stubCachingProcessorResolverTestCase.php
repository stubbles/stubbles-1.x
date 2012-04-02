<?php
/**
 * Tests for net::stubbles::websites::cache::stubCachingProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_cache_test
 * @version     $Id: stubCachingProcessorResolverTestCase.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::cache::stubCachingProcessorResolver');
/**
 * Tests for net::stubbles::websites::cache::stubCachingProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_cache_test
 * @deprecated
 * @group       websites
 * @group       websites_cache
 */
class stubCachingProcessorResolverTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubCachingProcessorResolver
     */
    protected $cachingProcessorResolver;
    /**
     * mocked processor resolver
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockProcessorResolver;
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
     * mocked website cache
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockWebsiteCache;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockProcessorResolver    = $this->getMock('stubProcessorResolver');
        $this->mockWebsiteCache         = $this->getMock('stubWebsiteCache');
        $this->cachingProcessorResolver = new stubCachingProcessorResolver($this->mockProcessorResolver, $this->mockWebsiteCache);
        $this->mockRequest              = $this->getMock('stubRequest');
        $this->mockSession              = $this->getMock('stubSession');
        $this->mockResponse             = $this->getMock('stubResponse');
    }

    /**
     * ssl should just be handled by cachable processor
     *
     * @test
     */
    public function annotationsPresent()
    {
        $constructor = $this->cachingProcessorResolver->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        
        $refParams = $constructor->getParameters();
        $this->assertTrue($refParams[0]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.websites.processor.defaultResolver', $refParams[0]->getAnnotation('Named')->getName());
    }

    /**
     * resolving a processor returns it decorated with caching processor
     *
     * @test
     */
    public function resolveProcessor()
    {
        $mockProcessor = $this->getMock('stubProcessor');
        $this->mockProcessorResolver->expects($this->once())
                                    ->method('resolve')
                                    ->with($this->equalTo($this->mockRequest),
                                           $this->equalTo($this->mockSession),
                                           $this->equalTo($this->mockResponse)
                                      )
                                    ->will($this->returnValue($mockProcessor));
        $cachingProcessor = $this->cachingProcessorResolver->resolve($this->mockRequest,
                                                                     $this->mockSession,
                                                                     $this->mockResponse
                            );
        $this->assertInstanceOf('stubCachingProcessor', $cachingProcessor);
        
        $this->mockProcessorResolver->expects($this->once())
                                    ->method('getInterceptorDescriptor')
                                    ->with($this->equalTo($this->mockRequest))
                                    ->will($this->returnValue('interceptor'));
        $this->assertEquals('interceptor',
                            $this->cachingProcessorResolver->getInterceptorDescriptor($this->mockRequest)
        );
    }
}
?>