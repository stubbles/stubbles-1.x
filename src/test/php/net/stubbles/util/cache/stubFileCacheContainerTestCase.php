<?php
/**
 * Tests for net::stubbles::util::cache::stubFileCacheContainer.
 *
 * @package     stubbles
 * @subpackage  util_cache_test
 * @version     $Id: stubFileCacheContainerTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::cache::stubFileCacheContainer');
@include_once 'vfsStream/vfsStream.php';
/**
 * Tests for net::stubbles::util::cache::stubFileCacheContainer.
 *
 * @package     stubbles
 * @subpackage  util_cache_test
 * @group       util_cache
 */
class stubFileCacheContainerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubFileCacheContainer
     */
    protected $cacheContainer;
    /**
     * a mocked cache strategy
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStrategy;
    /**
     * the path to the cache files
     *
     * @var  SimpleTestStreamDirectory
     */
    protected $cacheDirectory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('stubFileCacheContainerTestCase requires vfsStream, see http://vfs.bovigo.org/.');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('cache'));
        $this->mockCacheStrategy = $this->getMock('stubCacheStrategy');
        $this->cacheContainer    = new stubFileCacheContainer($this->mockCacheStrategy, vfsStream::url('cache/id'));
        $this->cacheDirectory    = vfsStreamWrapper::getRoot()->getChild('id');
    }

    /**
     * assert that put() works as expected
     *
     * @test
     */
    public function put()
    {
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('isCachable')
                                ->will($this->onConsecutiveCalls(true, false, true));
        
        $this->assertEquals(3, $this->cacheContainer->put('foo', 'bar'));
        $this->assertFalse($this->cacheContainer->put('baz', 'bar'));
        $this->assertTrue($this->cacheDirectory->hasChild('foo.cache'));
        $this->assertEquals('bar', $this->cacheDirectory->getChild('foo.cache')->getContent());
        $this->assertFalse($this->cacheDirectory->hasChild('baz.cache'));
        
        $this->assertEquals(3, $this->cacheContainer->put('foo', 'baz'));
        $this->assertTrue($this->cacheDirectory->hasChild('foo.cache'));
        $this->assertEquals('baz', $this->cacheDirectory->getChild('foo.cache')->getContent());
    }

    /**
     * assert that has() works as expected
     *
     * @test
     */
    public function has()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')->withContent('bar'));
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
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')->withContent('bar'));
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
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')->withContent('bar'));
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true, false));
        $this->assertEquals(3, $this->cacheContainer->getSize('foo'));
        $this->assertEquals(0, $this->cacheContainer->getSize('foo'));
        $this->assertEquals(0, $this->cacheContainer->getSize('bar'));
    }

    /**
     * assert that getUsedSpace() works as expected
     *
     * @test
     */
    public function getUsedSpace()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')->withContent('bar'));
        $this->assertEquals(3, $this->cacheContainer->getUsedSpace());
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isCachable')
                                ->will($this->returnValue(true));
        $this->cacheContainer->put('bar', 'baz');
        $this->assertEquals(6, $this->cacheContainer->getUsedSpace());
    }

    /**
     * assert that getKeys() works as expected
     *
     * @test
     */
    public function getKeys()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')->withContent('bar'));
        $this->mockCacheStrategy->expects($this->exactly(5))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, false, false, true, false));
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isCachable')
                                ->will($this->returnValue(true));
        $this->assertEquals(array('foo' => 'foo'), $this->cacheContainer->getKeys());
        $this->cacheContainer->put('bar', 'baz');
        $this->assertEquals(array('foo' => 'foo', 'bar' => 'bar'), $this->cacheContainer->getKeys());
        $this->assertEquals(array('bar' => 'bar'), $this->cacheContainer->getKeys());
    }

    /**
     * test the garbage collection
     *
     * @test
     */
    public function gc()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')->withContent('bar'));
        $this->mockCacheStrategy->expects($this->any())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(true));
        $this->mockCacheStrategy->expects($this->exactly(5))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true, false, false, false));
        $this->assertSame($this->cacheContainer, $this->cacheContainer->gc());
        $this->assertTrue($this->cacheDirectory->hasChild('foo.cache'));
        $this->assertSame($this->cacheContainer, $this->cacheContainer->gc());
        $this->assertFalse($this->cacheDirectory->hasChild('foo.cache'));
        $this->assertFalse($this->cacheContainer->has('foo'));
        $this->assertNull($this->cacheContainer->get('foo'));
        $this->assertEquals(0, $this->cacheContainer->getSize('foo'));
    }

    /**
     * test a key that contains a directory seperator
     *
     * @test
     */
    public function keyContainingDirectorySeperator()
    {
        $this->mockCacheStrategy->expects($this->any())
                                ->method('isCachable')
                                ->will($this->returnValue(true));
        $this->mockCacheStrategy->expects($this->any())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(true));
        $this->assertEquals(3, $this->cacheContainer->put('bar' . DIRECTORY_SEPARATOR . 'foo', 'bar'));
        $this->assertTrue($this->cacheDirectory->hasChild('barfoo.cache'));
        $this->assertEquals('bar', $this->cacheDirectory->getChild('barfoo.cache')->getContent());
        $this->mockCacheStrategy->expects($this->exactly(5))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, false, false, false, true));
        $this->assertTrue($this->cacheContainer->has('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals('bar', $this->cacheContainer->get('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals(array('barfoo' => 'barfoo'), $this->cacheContainer->getKeys());
        $this->assertEquals(3, $this->cacheContainer->getSize('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals(3, $this->cacheContainer->getUsedSpace());
        $this->assertSame($this->cacheContainer, $this->cacheContainer->gc());
        $this->assertFalse($this->cacheDirectory->hasChild('barfoo.cache'));
    }

    /**
     * test a key that contains a directory seperator
     *
     * @test
     */
    public function keyContainingDirectorySeperatorAndExistingCacheFile()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('barfoo.cache')->withContent('bar'));
        $this->assertTrue($this->cacheContainer->has('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals('bar', $this->cacheContainer->get('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals(array('barfoo' => 'barfoo'), $this->cacheContainer->getKeys());
        $this->assertEquals(3, $this->cacheContainer->getSize('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals(3, $this->cacheContainer->getUsedSpace());
    }
}
?>