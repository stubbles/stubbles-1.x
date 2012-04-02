<?php
/**
 * Test for net::stubbles::webapp::cache::stubWebsiteCacheProvider.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @version     $Id: stubWebsiteCacheProviderTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory',
                      'net::stubbles::webapp::cache::stubWebsiteCacheProvider'
);
/**
 * Test for net::stubbles::webapp::cache::stubWebsiteCacheProvider.
 *
 * @package     stubbles
 * @subpackage  webapp_cache_test
 * @since       1.7.0
 * @group       webapp
 * @group       webapp_cache
 */
class stubWebsiteCacheProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubWebsiteCacheProvider
     */
    protected $websiteCacheProvider;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInjector         = $this->getMock('stubInjector');
        $this->websiteCacheProvider = new stubWebsiteCacheProvider($this->mockInjector);
    }

    /**
     * @test
     */
    public function isDefaultProviderForWebsiteCache()
    {
        $refClass = new stubReflectionClass('net::stubbles::webapp::cache::stubWebsiteCache');
        $this->assertTrue($refClass->hasAnnotation('ProvidedBy'));
        $this->assertEquals($this->websiteCacheProvider->getClassName(),
                            $refClass->getAnnotation('ProvidedBy')
                                     ->getValue()
                                     ->getFullQualifiedClassName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->websiteCacheProvider->getClass()
                                                     ->getConstructor()
                                                     ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetLoggerMethod()
    {
        $setLoggerMethod = $this->websiteCacheProvider->getClass()->getMethod('setLogger');
        $this->assertTrue($setLoggerMethod->hasAnnotation('Inject'));
        $this->assertTrue($setLoggerMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setLoggerMethod->hasAnnotation('Named'));
        $this->assertEquals(stubLogger::LEVEL_INFO,
                            $setLoggerMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function createsCompositeWhenNoLoggerProvided()
    {
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->will($this->returnValue($this->getMock('stubCacheContainer')));
        $this->assertInstanceOf('stubCompositeWebsiteCache',
                                $this->websiteCacheProvider->get()
        );
    }

    /**
     * @test
     */
    public function decoratedWithLoggingWebsiteCacheIfLoggerProvided()
    {
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->will($this->returnValue($this->getMock('stubCacheContainer')));
        $this->websiteCacheProvider->setLogger(new stubLogger(new stubEmptyLogEntryFactory()));
        $this->assertInstanceOf('stubLoggingWebsiteCache',
                                $this->websiteCacheProvider->get()
        );
    }
}
?>