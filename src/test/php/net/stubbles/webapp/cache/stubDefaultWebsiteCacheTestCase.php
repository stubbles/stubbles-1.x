<?php
/**
 * Tests for net::stubbles::webapp::cache::stubDefaultWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @version     $Id: stubDefaultWebsiteCacheTestCase.php 3294 2011-12-17 23:26:20Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubDefaultWebsiteCache');
/**
 * Tests for net::stubbles::webapp::cache::stubDefaultWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @group       webapp
 * @group       webapp_cache
 */
class stubDefaultWebsiteCacheTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  TeststubDefaultWebsiteCache
     */
    protected $defaultWebsiteCache;
    /**
     * mocked cache container instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheContainer;
    /**
     * mocked response serializer
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponseSerializer;
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
     * set up test environment
     */
    public function setUp()
    {
        $this->mockCacheContainer     = $this->getMock('stubCacheContainer');
        $this->mockResponseSerializer = $this->getMock('stubResponseSerializer');
        $this->defaultWebsiteCache    = new stubDefaultWebsiteCache($this->mockCacheContainer,
                                                                    $this->mockResponseSerializer
                                        );
        $this->mockRequest            = $this->getMock('stubRequest');
        $this->mockResponse           = $this->getMock('stubResponse');
    }

    /**
     * @test
     */
    public function storeReturnsFalseIfCacheReturns0BytesWritten()
    {
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('serializeWithoutCookies')
                                     ->with($this->equalTo($this->mockResponse))
                                     ->will($this->returnValue('serialized'));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('put')
                                 ->will($this->returnValue(0));
        $this->assertFalse($this->defaultWebsiteCache->store($this->mockRequest, $this->mockResponse, 'baz'));
    }

    /**
     * @test
     */
    public function storeReturnsTrueIfCacheReturnsMoreThan0BytesWritten()
    {
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('serializeWithoutCookies')
                                     ->with($this->equalTo($this->mockResponse))
                                     ->will($this->returnValue('serialized'));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('put')
                                 ->will($this->returnValue(10));
        $this->assertTrue($this->defaultWebsiteCache->store($this->mockRequest, $this->mockResponse, 'baz'));
    }
}
?>