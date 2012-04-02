<?php
/**
 * Tests for net::stubbles::websites::ioc::stubProcessorResolverProvider.
 *
 * @package     stubbles
 * @subpackage  websites_ioc_test
 * @version     $Id: stubProcessorResolverProviderTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::ioc::stubProcessorResolverProvider');
/**
 * Tests for net::stubbles::websites::ioc::stubProcessorResolverProvider.
 *
 * @package     stubbles
 * @subpackage  websites_ioc_test
 * @group       websites
 * @group       websites_ioc
 */
class stubProcessorResolverProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubProcessorResolverProvider
     */
    protected $websiteBindingModule;
    /**
     * binder instance to be used
     *
     * @var   PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockMode;
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
        $this->mockMode             = $this->getMock('stubMode');
        $this->mockInjector         = $this->getMock('stubInjector');
        $this->websiteBindingModule = new stubProcessorResolverProvider($this->mockInjector);
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationsPresent()
    {
        $class = $this->websiteBindingModule->getClass();
        $this->assertTrue($class->getConstructor()->hasAnnotation('Inject'));
        
        $setModeMethod = $class->getMethod('setMode');
        $this->assertTrue($setModeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setModeMethod->getAnnotation('Inject')->isOptional());
    }

    /**
     * if caching is disabled the default processor resolver should be returned
     *
     * @test
     */
    public function returnsDefaultProcessorResolverIfCachingIsDisabled()
    {
        $mockProcessorResolver = new stdClass();
        $this->mockMode->expects($this->once())
                       ->method('isCacheEnabled')
                       ->will($this->returnValue(false));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('stubProcessorResolver'), $this->equalTo('net.stubbles.websites.processor.defaultResolver'))
                           ->will($this->returnValue($mockProcessorResolver));
        $this->websiteBindingModule->setMode($this->mockMode);
        $this->assertSame($mockProcessorResolver, $this->websiteBindingModule->get());
    }

    /**
     * if caching is enabled the caching processor resolver should be returned
     *
     * @test
     */
    public function returnsCachingProcessorResolverIfCachingIsEnabled()
    {
        $mockProcessorResolver = new stdClass();
        $this->mockMode->expects($this->once())
                       ->method('isCacheEnabled')
                       ->will($this->returnValue(true));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('net::stubbles::websites::cache::stubCachingProcessorResolver'))
                           ->will($this->returnValue($mockProcessorResolver));
        $this->websiteBindingModule->setMode($this->mockMode);
        $this->assertSame($mockProcessorResolver, $this->websiteBindingModule->get());
    }

    /**
     * if caching is enabled the caching processor resolver should be returned
     *
     * @test
     */
    public function returnsCachingProcessorResolverIfNoModeSet()
    {
        $mockProcessorResolver = new stdClass();
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('net::stubbles::websites::cache::stubCachingProcessorResolver'))
                           ->will($this->returnValue($mockProcessorResolver));
        $this->assertSame($mockProcessorResolver, $this->websiteBindingModule->get());
    }
}
?>