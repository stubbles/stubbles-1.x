<?php
/**
 * Tests for net::stubbles::util::cache::stubAbstractCacheContainer.
 *
 * @package     stubbles
 * @subpackage  util_cache_test
 * @version     $Id: stubAbstractCacheContainerTestCase.php 2060 2009-01-26 12:57:25Z mikey $
 */
stubClassLoader::load('net::stubbles::util::cache::stubAbstractCacheContainer');
class TeststubAbstractCacheContainer extends stubAbstractCacheContainer implements stubCacheContainer
{
    public function setStrategy(stubCacheStrategy $cacheStrategy)
    {
        $this->strategy = $cacheStrategy;
    }

    public $data = array();

    protected function doPut($key, $data)
    {
        $this->data[$key] = array('data' => $data, 'time' => time());
        return strlen($data);
    }

    protected function doHas($key)
    {
        return isset($this->data[$key]['data']);
    }

    protected function doGet($key)
    {
        if ($this->doHas($key) == true) {
            return $this->data[$key]['data'];
        }
        
        return null;
    }

    public function getLifeTime($key)
    {
        if ($this->doHas($key) == true) {
            return (time() - $this->data[$key]['time']);
        }
        
        return 0;
    }

    public function getStoreTime($key)
    {
        if ($this->doHas($key) == true) {
            return $this->data[$key]['time'];
        }
        
        return 0;
    }
    
    protected function doGetSize($key)
    {
        return strlen($this->data[$key]['data']);
    }

    public function getUsedSpace()
    {
        $size = 0;
        foreach ($this->data as $data) {
            $size += strlen($data['data']);
        }
        
        return $size;
    }

    public function getKeys()
    {
        return array_keys($this->data);
    }

    protected function doGc()
    {
        foreach ($this->data as $key => $data) {
            if ($this->strategy->isExpired($this, $key) == true) {
                unset($this->data[$key]);
            }
        }
    }
}
/**
 * Tests for net::stubbles::util::cache::stubAbstractCacheContainer.
 *
 * @package     stubbles
 * @subpackage  util_cache_test
 * @group       util_cache
 */
class stubAbstractCacheContainerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubAbstractCacheContainer
     */
    protected $cacheContainer;
    /**
     * a mocked cache strategy
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStrategy;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockCacheStrategy = $this->getMock('stubCacheStrategy');
        $this->cacheContainer    = new TeststubAbstractCacheContainer();
        $this->cacheContainer->setStrategy($this->mockCacheStrategy);
    }

    /**
     * assert that put() works as expected
     *
     * @test
     */
    public function put()
    {
        $this->mockCacheStrategy->expects($this->exactly(2))
                                ->method('isCachable')
                                ->will($this->onConsecutiveCalls(true, false));
        $this->assertEquals(3, $this->cacheContainer->put('foo', 'bar'));
        $this->assertEquals(false, $this->cacheContainer->put('baz', 'bar'));
        $this->assertEquals('bar', $this->cacheContainer->data['foo']['data']);
        $this->assertEquals(array('foo'), $this->cacheContainer->getKeys());
    }

    /**
     * assert that has() works as expected
     *
     * @test
     */
    public function has()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true, false));
        $this->assertTrue($this->cacheContainer->has('foo'));
        $this->assertFalse($this->cacheContainer->has('foo'));
        $this->assertFalse($this->cacheContainer->has('bar'));
    }

    /**
     * assert that get() works as expected
     *
     * @test
     */
    public function get()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true, false));
        $this->assertEquals('bar', $this->cacheContainer->get('foo'));
        $this->assertNull($this->cacheContainer->get('foo'));
        $this->assertNull($this->cacheContainer->get('bar'));
    }

    /**
     * assert that getSize() works as expected
     *
     * @test
     */
    public function getSize()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true, false));
        $this->assertEquals(3, $this->cacheContainer->getSize('foo'));
        $this->assertEquals(0, $this->cacheContainer->getSize('foo'));
        $this->assertEquals(0, $this->cacheContainer->getSize('bar'));
    }

    /**
     * test the garbage collection
     *
     * @test
     */
    public function gc()
    {
        $this->mockCacheStrategy->expects($this->exactly(2))
                                ->method('shouldRunGc')
                                ->will($this->onConsecutiveCalls(false, true));
        $this->assertNull($this->cacheContainer->lastGcRun());
        $this->assertSame($this->cacheContainer, $this->cacheContainer->gc());
        $this->assertNull($this->cacheContainer->lastGcRun());
        
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->assertSame($this->cacheContainer, $this->cacheContainer->gc());
        $this->assertEquals(array(), $this->cacheContainer->data);
        $this->assertNotNull($this->cacheContainer->lastGcRun());
    }
}
?>