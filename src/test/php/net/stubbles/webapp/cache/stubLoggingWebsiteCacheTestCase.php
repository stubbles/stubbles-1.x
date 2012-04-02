<?php
/**
 * Test for net::stubbles::webapp::cache::stubLoggingWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @version     $Id: stubLoggingWebsiteCacheTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::appender::stubMemoryLogAppender',
                      'net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory',
                      'net::stubbles::webapp::cache::stubLoggingWebsiteCache'
);
/**
 * Test for net::stubbles::webapp::cache::stubLoggingWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @since       1.7.0
 * @group       webapp
 * @group       webapp_cache
 * @group       bug265
 */
class stubLoggingWebsiteCacheTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLoggingWebsiteCache
     */
    protected $loggingWebsiteCache;
    /**
     * mocked website cache
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockWebsiteCache;
    /**
     * log appender
     *
     * @var  stubMemoryLogAppender
     */
    protected $memoryLogAppender;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockWebsiteCache  = $this->getMock('stubWebsiteCache');
        $logger                  = new stubLogger(new stubEmptyLogEntryFactory());
        $this->memoryLogAppender = new stubMemoryLogAppender();
        $logger->addLogAppender($this->memoryLogAppender);
        $this->loggingWebsiteCache = new stubLoggingWebsiteCache($this->mockWebsiteCache, $logger);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->loggingWebsiteCache->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $this->assertTrue($constructor->hasAnnotation('Named'));
        $this->assertEquals(stubLogger::LEVEL_INFO,
                            $constructor->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function addCacheVarDispatchesToDecoratedInstance()
    {
        $this->mockWebsiteCache->expects($this->once())
                               ->method('addCacheVar')
                               ->with($this->equalTo('foo'), $this->equalTo(303));
        $this->assertSame($this->loggingWebsiteCache,
                          $this->loggingWebsiteCache->addCacheVar('foo', 303)
        );
    }

    /**
     * @test
     */
    public function addCacheVarsDispatchesToDecoratedInstance()
    {
        $this->mockWebsiteCache->expects($this->once())
                               ->method('addCacheVars')
                               ->with($this->equalTo(array('foo' => 303)));
        $this->assertSame($this->loggingWebsiteCache,
                          $this->loggingWebsiteCache->addCacheVars(array('foo' => 303))
        );
    }

    /**
     * @test
     */
    public function retrievalMissIsLoggedWithReason()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache->expects($this->once())
                               ->method('retrieve')
                               ->with($this->equalTo($mockRequest),
                                      $this->equalTo($mockResponse),
                                      $this->equalTo('index')
                                 )
                               ->will($this->returnValue('miss reason'));
        $this->mockWebsiteCache->expects($this->once())
                               ->method('getClassName')
                               ->will($this->returnValue('mockWebsiteCache1'));
        $this->assertEquals('miss reason',
                            $this->loggingWebsiteCache->retrieve($mockRequest, $mockResponse, 'index')
        );

        $this->assertEquals(1, $this->memoryLogAppender->countLogEntries('cache'));

        $this->assertEquals(array('index',
                                  'miss',
                                  'mockWebsiteCache1',
                                  'miss reason'
                            ),
                            $this->memoryLogAppender->getLogEntryData('cache', 0)
        );
    }

    /**
     * @test
     */
    public function retrievalHitIsLoggedWithReason()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache->expects($this->once())
                               ->method('retrieve')
                               ->with($this->equalTo($mockRequest),
                                      $this->equalTo($mockResponse),
                                      $this->equalTo('index')
                                 )
                               ->will($this->returnValue(true));
        $this->mockWebsiteCache->expects($this->once())
                               ->method('getClassName')
                               ->will($this->returnValue('mockWebsiteCache1'));
        $this->assertTrue($this->loggingWebsiteCache->retrieve($mockRequest, $mockResponse, 'index'));

        $this->assertEquals(1, $this->memoryLogAppender->countLogEntries('cache'));

        $this->assertEquals(array('index',
                                  'hit',
                                  'mockWebsiteCache1',
                                  ''
                            ),
                            $this->memoryLogAppender->getLogEntryData('cache', 0)
        );
    }

    /**
     * @test
     */
    public function storeDispatchesToDecoratedInstance()
    {
        $mockRequest  = $this->getMock('stubRequest');
        $mockResponse = $this->getMock('stubResponse');
        $this->mockWebsiteCache->expects($this->once())
                               ->method('store')
                               ->with($this->equalTo($mockRequest),
                                      $this->equalTo($mockResponse),
                                      $this->equalTo('index')
                                 )
                               ->will($this->returnValue(true));
        $this->assertTrue($this->loggingWebsiteCache->store($mockRequest, $mockResponse, 'index'));
    }
}
?>