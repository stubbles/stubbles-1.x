<?php
/**
 * Test for net::stubbles::webapp::cache::stubCompositeWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @version     $Id: stubCompositeWebsiteCacheTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubCompositeWebsiteCache');
/**
 * Test for net::stubbles::webapp::cache::stubCompositeWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @since       1.7.0
 * @group       webapp
 * @group       webapp_cache
 * @group       bug265
 */
class stubCompositeWebsiteCacheTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubCompositeWebsiteCache
     */
    protected $compositeWebsiteCache;
    /**
     * mocked website cache
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockWebsiteCache1;
    /**
     * mocked website cache
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockWebsiteCache2;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->compositeWebsiteCache = new stubCompositeWebsiteCache();
        $this->mockWebsiteCache1     = $this->compositeWebsiteCache->addWebsiteCache($this->getMock('stubWebsiteCache'));
        $this->mockWebsiteCache2     = $this->compositeWebsiteCache->addWebsiteCache($this->getMock('stubWebsiteCache'));
    }

    /**
     * @test
     */
    public function cacheVarIsAddedToAllCombinedInstances()
    {
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('addCacheVar')
                                ->with($this->equalTo('foo'), $this->equalTo(303));
        $this->mockWebsiteCache2->expects($this->once())
                                ->method('addCacheVar')
                                ->with($this->equalTo('foo'), $this->equalTo(303));
        $this->assertSame($this->compositeWebsiteCache,
                          $this->compositeWebsiteCache->addCacheVar('foo', 303)
        );
    }

    /**
     * @test
     */
    public function cacheVarsAreAddedToAllCombinedInstances()
    {
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('addCacheVars')
                                ->with($this->equalTo(array('foo' => 303)));
        $this->mockWebsiteCache2->expects($this->once())
                                ->method('addCacheVars')
                                ->with($this->equalTo(array('foo' => 303)));
        $this->assertSame($this->compositeWebsiteCache,
                          $this->compositeWebsiteCache->addCacheVars(array('foo' => 303))
        );
    }

    /**
     * @test
     */
    public function retrieveReturnsCombinedMissReasonIfNoneAddedCacheWasAbleToRetrieveResponse()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('retrieve')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue('no cache file'));
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('getClassName')
                                ->will($this->returnValue('mockWebsiteCache1'));
        $this->mockWebsiteCache2->expects($this->once())
                                ->method('retrieve')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue('not accepted'));
        $this->mockWebsiteCache2->expects($this->once())
                                ->method('getClassName')
                                ->will($this->returnValue('mockWebsiteCache2'));
        $this->assertEquals('mockWebsiteCache1: no cache file;mockWebsiteCache2: not accepted',
                            $this->compositeWebsiteCache->retrieve($mockRequest, $mockResponse, 'index')
        );
    }

    /**
     * @test
     */
    public function retrieveReturnsTrueIfAtLeastOneAddedCacheWasAbleToRetrieveResponse()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('retrieve')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(false));
        $this->mockWebsiteCache2->expects($this->once())
                                ->method('retrieve')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(true));
        $this->assertTrue($this->compositeWebsiteCache->retrieve($mockRequest, $mockResponse, 'index'));
    }

    /**
     * @test
     */
    public function retrieveStopsAfterFirstAddedCacheWasAbleToRetrieveResponse()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('retrieve')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(true));
        $this->mockWebsiteCache2->expects($this->never())
                                ->method('retrieve');
        $this->assertTrue($this->compositeWebsiteCache->retrieve($mockRequest, $mockResponse, 'index'));
    }

    /**
     * @test
     */
    public function storeReturnsFalseIfNoneAddedCacheWasAbleToStoreResponse()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('store')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(false));
        $this->mockWebsiteCache2->expects($this->once())
                                ->method('store')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(false));
        $this->assertFalse($this->compositeWebsiteCache->store($mockRequest, $mockResponse, 'index'));
    }

    /**
     * @test
     */
    public function storeReturnsTrueIfAtLeastOneAddedCacheWasAbleToStoreResponse()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('store')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(false));
        $this->mockWebsiteCache2->expects($this->once())
                                ->method('store')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(true));
        $this->assertTrue($this->compositeWebsiteCache->store($mockRequest, $mockResponse, 'index'));
    }

    /**
     * @test
     */
    public function storeReturnsTrueIfAllAddedCachesWereAbleToStoreResponse()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache1->expects($this->once())
                                ->method('store')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(true));
        $this->mockWebsiteCache2->expects($this->once())
                                ->method('store')
                                ->with($this->equalTo($mockRequest),
                                       $this->equalTo($mockResponse),
                                       $this->equalTo('index')
                                  )
                                ->will($this->returnValue(true));
        $this->assertTrue($this->compositeWebsiteCache->store($mockRequest, $mockResponse, 'index'));
    }
}
?>