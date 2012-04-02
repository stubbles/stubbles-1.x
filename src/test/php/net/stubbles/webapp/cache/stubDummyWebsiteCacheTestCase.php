<?php
/**
 * Tests for net::stubbles::webapp::cache::stubDummyWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @version     $Id: stubDummyWebsiteCacheTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubDummyWebsiteCache');
/**
 * Tests for net::stubbles::webapp::cache::stubDummyWebsiteCache.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @group       webapp
 * @group       webapp_cache
 */
class stubDummyWebsiteCacheTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  stubDummyWebsiteCache
     */
    protected $dummyWebsiteCache;
    
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->dummyWebsiteCache = new stubDummyWebsiteCache();
    }

    /**
     * assert that cache variables are handled correct
     *
     * @test
     */
    public function cacheVars()
    {
        $this->assertEquals(array(), $this->dummyWebsiteCache->getCacheVars());
        $this->dummyWebsiteCache->addCacheVar('foo', 'bar');
        $this->assertEquals(array('foo' => 'bar'), $this->dummyWebsiteCache->getCacheVars());
        $this->dummyWebsiteCache->addCacheVars(array('bar' => 'baz'));
        $this->assertEquals(array('foo' => 'bar',
                                  'bar' => 'baz'
                            ),
                            $this->dummyWebsiteCache->getCacheVars()
        );
    }

    /**
     * retrieve does not work for dummy website cache
     *
     * @test
     */
    public function retrieveAlwaysReturnsFalse()
    {
        $this->assertFalse($this->dummyWebsiteCache->retrieve($this->getMock('stubRequest'), $this->getMock('stubResponse'), 'foo'));
    }

    /**
     * assure does not work for dummy website cache
     *
     * @test
     */
    public function storeAlwaysReturnsFalse()
    {
        $this->assertFalse($this->dummyWebsiteCache->store($this->getMock('stubRequest'), $this->getMock('stubResponse'), 'foo'));
    }
}
?>