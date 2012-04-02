<?php
/**
 * Tests for net::stubbles::webapp::cache::stubCachingProcessor.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @version     $Id: stubCachingProcessorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubCachingProcessor');
/**
 * Tests for net::stubbles::webapp::cache::stubCachingProcessor.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @group       webapp
 * @group       webapp_cache
 */
class stubCachingProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  stubCachingProcessor
     */
    protected $cachingProcessor;
    /**
     * mocked processor
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
     * mocked website cache instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockWebsiteCache;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockProcessor    = $this->getMock('stubProcessor');
        $this->mockRequest      = $this->getMock('stubRequest');
        $this->mockResponse     = $this->getMock('stubResponse');
        $this->mockWebsiteCache = $this->getMock('stubWebsiteCache');
        $this->cachingProcessor = new stubCachingProcessor($this->mockProcessor,
                                                           $this->mockRequest,
                                                           $this->mockResponse,
                                                           $this->mockWebsiteCache
                                  );
    }

    /**
     * ssl should just be handled by cachable processor
     *
     * @test
     */
    public function sslHandling()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('forceSsl')
                            ->will($this->returnValue(true));
        $this->mockProcessor->expects($this->once())
                            ->method('isSsl')
                            ->will($this->returnValue(false));
        $this->assertTrue($this->cachingProcessor->forceSsl());
        $this->assertFalse($this->cachingProcessor->isSsl());
    }

    /**
     * generated content is cachable and already cached
     *
     * @test
     */
    public function cachableAndCached()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('isSsl')
                            ->will($this->returnValue(false));
        $this->mockProcessor->expects($this->once())
                            ->method('isCachable')
                            ->will($this->returnValue(true));
        $this->mockProcessor->expects($this->once())
                            ->method('getCacheVars')
                            ->will($this->returnValue(array('foo' => 'bar')));
        $this->mockProcessor->expects($this->once())
                            ->method('getRouteName')
                            ->will($this->returnValue('routeName'));
        $this->mockWebsiteCache->expects($this->once())
                               ->method('addCacheVars')
                               ->with($this->equalTo(array('foo' => 'bar')));
        $this->mockWebsiteCache->expects($this->once())
                               ->method('addCacheVar')
                               ->with($this->equalTo('ssl'), $this->equalTo(false));
        $this->mockWebsiteCache->expects($this->once())
                               ->method('retrieve')
                               ->with($this->equalTo($this->mockRequest), $this->equalTo($this->mockResponse), $this->equalTo('routeName'))
                               ->will($this->returnValue(true));
        $this->mockProcessor->expects($this->never())
                            ->method('process');
        $this->mockWebsiteCache->expects($this->never())
                               ->method('store');
        $this->cachingProcessor->process();
    }

    /**
     * generated content is cachable but not cached
     *
     * @test
     */
    public function cachableAndNotCached()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('isSsl')
                            ->will($this->returnValue(true));
        $this->mockProcessor->expects($this->once())
                            ->method('isCachable')
                            ->will($this->returnValue(true));
        $this->mockProcessor->expects($this->once())
                            ->method('getCacheVars')
                            ->will($this->returnValue(array('foo' => 'bar')));
        $this->mockProcessor->expects($this->exactly(2))
                            ->method('getRouteName')
                            ->will($this->returnValue('routeName'));
        $this->mockWebsiteCache->expects($this->once())
                               ->method('addCacheVars')
                               ->with($this->equalTo(array('foo' => 'bar')));
        $this->mockWebsiteCache->expects($this->once())
                               ->method('addCacheVar')
                               ->with($this->equalTo('ssl'), $this->equalTo(true));
        $this->mockWebsiteCache->expects($this->once())
                               ->method('retrieve')
                               ->with($this->equalTo($this->mockRequest), $this->equalTo($this->mockResponse), $this->equalTo('routeName'))
                               ->will($this->returnValue(false));
        $this->mockProcessor->expects($this->once())
                            ->method('process');
        $this->mockWebsiteCache->expects($this->once())
                               ->method('store')
                               ->with($this->equalTo($this->mockRequest), $this->equalTo($this->mockResponse), $this->equalTo('routeName'));
        $this->cachingProcessor->process();
    }

    /**
     * generated content is not cachable
     *
     * @test
     */
    public function notCachable()
    {
        $this->mockProcessor->expects($this->never())
                                    ->method('isSsl');
        $this->mockProcessor->expects($this->once())
                            ->method('isCachable')
                            ->will($this->returnValue(false));
        $this->mockProcessor->expects($this->never())
                            ->method('addCacheVars');
        $this->mockProcessor->expects($this->never())
                            ->method('getRouteName');
        $this->mockWebsiteCache->expects($this->never())
                               ->method('addCacheVar');
        $this->mockWebsiteCache->expects($this->never())
                               ->method('retrieve')
                               ->will($this->returnValue(false));
        $this->mockProcessor->expects($this->once())
                            ->method('process');
        $this->mockWebsiteCache->expects($this->never())
                               ->method('store');
        $this->cachingProcessor->process();
    }
}
?>