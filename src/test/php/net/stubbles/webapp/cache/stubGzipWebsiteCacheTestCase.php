<?php
/**
 * Tests for net::stubbles::webapp::cache::stubGzipWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @version     $Id: stubGzipWebsiteCacheTestCase.php 3294 2011-12-17 23:26:20Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubGzipWebsiteCache');
/**
 * Helper class for unit test to access some methods without using implementation
 * of the parent class.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 */
class TeststubGzipWebsiteCache extends stubGzipWebsiteCache
{
    /**
     * generates the cache key from given list of cache keys
     *
     * @param   string  $routeName  name of the route to be cached
     * @return  string
     */
    protected function generateCacheKey($routeName)
    {
        return $routeName;
    }
}
/**
 * Tests for net::stubbles::webapp::cache::stubGzipWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @group       webapp
 * @group       webapp_cache
 */
class stubGzipWebsiteCacheTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  TeststubGzipWebsiteCache
     */
    protected $gzipWebsiteCache;
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
        $this->gzipWebsiteCache       = new TeststubGzipWebsiteCache($this->mockCacheContainer,
                                                                     $this->mockResponseSerializer
                                        );
        $this->mockRequest            = $this->getMock('stubRequest');
        $this->mockResponse           = $this->getMock('stubResponse');
    }

    /**
     * @test
     */
    public function retrieveReturnsMissWhenCookiesNotAccepted()
    {
        $this->mockRequest->expects($this->once())
                          ->method('acceptsCookies')
                          ->will($this->returnValue(false));
        $this->mockResponse->expects($this->never())
                           ->method('addHeader');
        $this->mockResponse->expects($this->never())
                           ->method('write');
        $this->assertEquals('user agent does not accept cookies',
                            $this->gzipWebsiteCache->retrieve($this->mockRequest, $this->mockResponse, 'foo')
        );
    }

    /**
     * @test
     */
    public function retrieveReturnsMissWhenCompressionNotAccepted()
    {
        $validatingRequestValue = new stubValidatingRequestValue('HTTP_ACCEPT_ENCODING', 'invalid');
        $this->mockRequest->expects($this->once())
                          ->method('acceptsCookies')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->any())
                          ->method('validateHeader')
                          ->will($this->returnValue($validatingRequestValue));
        $this->mockResponse->expects($this->never())
                           ->method('addHeader');
        $this->mockResponse->expects($this->never())
                           ->method('write');
        $this->assertEquals('user agent does not accept compressed content',
                            $this->gzipWebsiteCache->retrieve($this->mockRequest, $this->mockResponse, 'foo')
        );
    }

    /**
     * @test
     */
    public function retrieveXGzipCompression()
    {
        $validatingRequestValue = new stubValidatingRequestValue('HTTP_ACCEPT_ENCODING',
                                                                 'x-gzip,gzip'
                                  );
        $this->mockRequest->expects($this->once())
                          ->method('acceptsCookies')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('validateHeader')
                          ->will($this->returnValue($validatingRequestValue));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('get')
                                 ->will($this->returnValue('cachedContents'));
        $cachedResponse = new stubBaseResponse();
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('unserialize')
                                     ->will($this->returnValue($cachedResponse));
        $this->mockResponse->expects($this->once())
                           ->method('merge')
                           ->with($this->equalTo($cachedResponse))
                           ->will($this->returnValue($this->mockResponse));
        $this->mockResponse->expects($this->at(2))
                           ->method('addHeader')
                           ->with($this->equalTo('Content-Encoding'), $this->equalTo(stubGzipWebsiteCache::X_GZIP));
        $this->mockResponse->expects($this->once())
                           ->method('replaceBody')
                           ->with(($this->equalTo(stubGzipWebsiteCache::HEADER)));
        $this->assertTrue($this->gzipWebsiteCache->retrieve($this->mockRequest, $this->mockResponse, 'foo'));
    }

    /**
     * @test
     */
    public function retrieveGzipCompression()
    {
        $validatingRequestValue = new stubValidatingRequestValue('HTTP_ACCEPT_ENCODING',
                                                                 stubGzipWebsiteCache::GZIP
                                  );
        $this->mockRequest->expects($this->once())
                          ->method('acceptsCookies')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->exactly(2))
                          ->method('validateHeader')
                          ->will($this->returnValue($validatingRequestValue));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('get')
                                 ->will($this->returnValue('cachedContents'));
        $cachedResponse = new stubBaseResponse();
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('unserialize')
                                     ->will($this->returnValue($cachedResponse));
        $this->mockResponse->expects($this->once())
                           ->method('merge')
                           ->with($this->equalTo($cachedResponse))
                           ->will($this->returnValue($this->mockResponse));
        $this->mockResponse->expects($this->at(2))
                           ->method('addHeader')
                           ->with($this->equalTo('Content-Encoding'), $this->equalTo(stubGzipWebsiteCache::GZIP));
        $this->mockResponse->expects($this->once())
                           ->method('replaceBody')
                           ->with(($this->equalTo(stubGzipWebsiteCache::HEADER)));
        $this->assertTrue($this->gzipWebsiteCache->retrieve($this->mockRequest, $this->mockResponse, 'foo'));
    }

    /**
     * @test
     */
    public function storeRemovesSessionIdentifiersBeforeCaching()
    {
        $this->mockResponse->expects($this->once())
                           ->method('getVersion')
                           ->will($this->returnValue('1.1'));
        $this->mockResponse->expects($this->once())
                           ->method('getStatusCode')
                           ->will($this->returnValue(200));
        $this->mockResponse->expects($this->once())
                           ->method('getHeaders')
                           ->will($this->returnValue(array()));
        $this->mockResponse->expects($this->once())
                           ->method('getCookies')
                           ->will($this->returnValue(array()));
        $this->mockResponse->expects($this->exactly(2))
                           ->method('getBody')
                           ->will($this->returnValue('fooContent$SIDbla$SESSION_NAMEblub$SESSION_ID'));
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('serializeWithoutCookies')
                                     ->with($this->logicalNot($this->equalTo($this->mockResponse)))
                                     ->will($this->returnValue('serialized'));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('put')
                                 ->with($this->equalTo('foo'), $this->equalTo('serialized'))
                                 ->will($this->returnValue(10));
        $this->assertTrue($this->gzipWebsiteCache->store($this->mockRequest, $this->mockResponse, 'foo'));
    }
}
?>