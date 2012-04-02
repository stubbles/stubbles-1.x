<?php
/**
 * Tests for net::stubbles::webapp::cache::stubAbstractWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @version     $Id: stubAbstractWebsiteCacheTestCase.php 3294 2011-12-17 23:26:20Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::appender::stubMemoryLogAppender',
                      'net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory',
                      'net::stubbles::webapp::cache::stubAbstractWebsiteCache'
);
/**
 * Helper class for unit test to access some methods without using implementation
 * of the parent class.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 */
abstract class TeststubAbstractWebsiteCache extends stubAbstractWebsiteCache
{
    /**
     * returns list of cache variables
     *
     * @return  array<string,scalar>
     */
    public function retrieveCacheVars()
    {
        return $this->cacheVars;
    }
}
/**
 * Tests for net::stubbles::webapp::cache::stubAbstractWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @group       webapp
 * @group       webapp_cache
 */
class stubAbstractWebsiteCacheTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  TeststubAbstractWebsiteCache
     */
    protected $abstractWebsiteCache;
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
        $this->abstractWebsiteCache   = $this->getMock('TeststubAbstractWebsiteCache',
                                                       array('prepareResponse'),
                                                       array($this->mockCacheContainer,
                                                             $this->mockResponseSerializer
                                                       )
                                        );
        $this->mockRequest            = $this->getMock('stubRequest');
        $this->mockResponse           = $this->getMock('stubResponse');
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->abstractWebsiteCache->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[0]->hasAnnotation('Named'));
        $this->assertEquals('websites', $parameters[0]->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function hasNoCacheVarsByDefault()
    {
        $this->assertEquals(array(), $this->abstractWebsiteCache->retrieveCacheVars());
    }

    /**
     * @test
     */
    public function cacheVarsAreStored()
    {
        $this->assertEquals(array('foo' => 'bar',
                                  'bar' => 'baz'
                            ),
                            $this->abstractWebsiteCache->addCacheVar('foo', 'bar')
                                                       ->addCacheVars(array('bar' => 'baz'))
                                                       ->retrieveCacheVars()
        );
    }

    /**
     * @test
     */
    public function cachedEntryMissingReturnsMissReason()
    {
        $this->mockCacheContainer->expects($this->once())
                                 ->method('has')
                                 ->will($this->returnValue(false));
        $this->mockResponseSerializer->expects($this->never())
                                     ->method('unserialize');
        $this->mockResponse->expects($this->never())
                           ->method('merge');
        $this->mockResponse->expects($this->never())
                           ->method('addHeader');
        $this->assertEquals('no cache file',
                            $this->abstractWebsiteCache->retrieve($this->mockRequest,
                                                                  $this->mockResponse,
                                                                  'baz'
                                                         )
        );
    }

    /**
     * @test
     */
    public function cachedEntryFoundButFailedToSetReturnsExceptionMessageAsMissReason()
    {
        $this->mockCacheContainer->expects($this->once())
                                 ->method('has')
                                 ->will($this->returnValue(true));
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('unserialize')
                                     ->will($this->throwException(new stubException('failed')));
        $this->mockResponse->expects($this->never())
                           ->method('merge');
        $this->mockResponse->expects($this->never())
                           ->method('addHeader');
        $this->assertEquals('failed',
                            $this->abstractWebsiteCache->retrieve($this->mockRequest,
                                                                  $this->mockResponse,
                                                                  'baz'
                                                         )
        );
    }

    /**
     * @test
     */
    public function cachedEntryFoundReturnsTrue()
    {
        $this->mockCacheContainer->expects($this->once())
                                 ->method('has')
                                 ->will($this->returnValue(true));
        $cachedResponse = new stubBaseResponse();
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('unserialize')
                                     ->will($this->returnValue($cachedResponse));
        $this->mockResponse->expects($this->once())
                           ->method('merge')
                           ->with($this->equalTo($cachedResponse))
                           ->will($this->returnValue($this->mockResponse));
        $this->mockResponse->expects($this->once())
                           ->method('addHeader');
        $this->assertTrue($this->abstractWebsiteCache->retrieve($this->mockRequest,
                                                                $this->mockResponse,
                                                                'baz'
                                                       )
        );
    }

    /**
     * @test
     */
    public function storeReturnsFalseIfCacheReturns0BytesWritten()
    {
        $this->abstractWebsiteCache->expects($this->once())
                                   ->method('prepareResponse')
                                   ->with($this->equalTo($this->mockResponse))
                                   ->will($this->returnValue($this->mockResponse));
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('serializeWithoutCookies')
                                     ->with($this->equalTo($this->mockResponse))
                                     ->will($this->returnValue('serialized'));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('put')
                                 ->will($this->returnValue(0));
        $this->assertFalse($this->abstractWebsiteCache->store($this->mockRequest, $this->mockResponse, 'baz'));
    }

    /**
     * @test
     */
    public function storeReturnsTrueIfCacheReturnsMoreThan0BytesWritten()
    {
        $this->abstractWebsiteCache->expects($this->once())
                                   ->method('prepareResponse')
                                   ->with($this->equalTo($this->mockResponse))
                                   ->will($this->returnValue($this->mockResponse));
        $this->mockResponseSerializer->expects($this->once())
                                     ->method('serializeWithoutCookies')
                                     ->with($this->equalTo($this->mockResponse))
                                     ->will($this->returnValue('serialized'));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('put')
                                 ->will($this->returnValue(10));
        $this->assertTrue($this->abstractWebsiteCache->store($this->mockRequest, $this->mockResponse, 'baz'));
    }
}
?>