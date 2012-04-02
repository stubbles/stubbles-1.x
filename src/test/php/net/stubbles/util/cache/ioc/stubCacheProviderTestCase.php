<?php
/**
 * Test for net::stubbles::util::cache::ioc::stubCacheProvider.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc_test
 * @version     $Id: stubCacheProviderTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::util::cache::ioc::stubCacheProvider');
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::util::cache::ioc::stubCacheProvider.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc_test
 * @group       util_cache
 * @group       util_cache_ioc
 */
class stubCacheProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubCacheProvider
     */
    protected $cacheProvider;
    /**
     * mocked cache strategy
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStrategy;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('stubFileCacheContainerTestCase requires vfsStream, see http://vfs.bovigo.org/.');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('cache'));
        $this->mockCacheStrategy = $this->getMock('stubCacheStrategy');
        $this->cacheProvider     = new stubCacheProvider($this->mockCacheStrategy, vfsStream::url('cache'));
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationPresentOnConstructor()
    {
        $refConstructor = $this->cacheProvider->getClass()->getConstructor();
        $this->assertTrue($refConstructor->hasAnnotation('Inject'));

        $refParams = $refConstructor->getParameters();
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.cache.path', $refParams[1]->getAnnotation('Named')->getName());
        
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationPresentOnSetFileModeMethod()
    {
        $refMethod = $this->cacheProvider->getClass()->getMethod('setFileMode');
        $this->assertTrue($refMethod->hasAnnotation('Inject'));
        $this->assertTrue($refMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.util.cache.filemode', $refMethod->getAnnotation('Named')->getName());
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function isDefaultProviderForCacheContainer()
    {
        $refClass = new stubReflectionClass('net::stubbles::util::cache::stubCacheContainer');
        $this->assertTrue($refClass->hasAnnotation('ProvidedBy'));
        $this->assertEquals($this->cacheProvider->getClassName(),
                            $refClass->getAnnotation('ProvidedBy')
                                     ->getProviderClass()
                                     ->getFullQualifiedClassName()
        );
    }

    /**
     * setting file mode returns the instance
     *
     * @test
     */
    public function fileMode()
    {
        $this->assertSame($this->cacheProvider, $this->cacheProvider->setFileMode(0660));
    }

    /**
     * named cache container is always the same instance
     *
     * @test
     */
    public function namedCacheContainerIsAlwaysSameInstance()
    {
        $this->mockCacheStrategy->expects($this->exactly(2))
                                ->method('shouldRunGc')
                                ->will($this->returnValue(false));
        $namedCacheContainer = $this->cacheProvider->get('websites');
        $this->assertInstanceOf('stubFileCacheContainer', $namedCacheContainer);
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('websites'));
        $this->assertSame($namedCacheContainer, $this->cacheProvider->get('websites'));
    }
    

    /**
     * unnamed cache container is always the same instance
     *
     * @test
     */
    public function unNamedCacheContainerIsAlwaysSameInstance()
    {
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('shouldRunGc')
                                ->will($this->returnValue(false));
        $unnamedCacheContainer = $this->cacheProvider->get();
        $this->assertInstanceOf('stubFileCacheContainer', $unnamedCacheContainer);
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild(stubCacheProvider::DEFAULT_NAME));
        $this->assertSame($unnamedCacheContainer, $this->cacheProvider->get());
        $this->assertSame($unnamedCacheContainer, $this->cacheProvider->get(stubCacheProvider::DEFAULT_NAME));
    }
}
?>