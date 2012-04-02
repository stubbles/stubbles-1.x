<?php
/**
 * Tests for net::stubbles::websites::ioc::stubWebsiteBindingModule.
 *
 * @package     stubbles
 * @subpackage  websites_ioc_test
 * @version     $Id: stubWebsiteBindingModuleTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubDefaultMode',
                      'net::stubbles::websites::ioc::stubWebsiteBindingModule'
);
require_once dirname(__FILE__) . '/../processors/DefaultProcessorResolverPropertyAccessor.php';
require_once dirname(__FILE__) . '/../processors/SimpleProcessorResolverPropertyAccessor.php';
/**
 * Tests for net::stubbles::websites::ioc::stubWebsiteBindingModule.
 *
 * @package     stubbles
 * @subpackage  websites_ioc_test
 * @group       websites
 * @group       websites_ioc
 */
class stubWebsiteBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubWebsiteBindingModule
     */
    protected $websiteBindingModule;
    /**
     * binder instance to be used
     *
     * @var  stubBinder
     */
    protected $binder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->websiteBindingModule = stubWebsiteBindingModule::create('foo', 'org::stubbles::test::FooProcessor');
        $this->binder               = new stubBinder();
        $this->binder->bind('stubInjector')->toInstance($this->binder->getInjector());
    }

    /**
     * create a binding module with xml processor as default and enabled jsonrpc and rss processor
     *
     * @test
     */
    public function createWithXmlProcessorAsDefault()
    {
        stubWebsiteBindingModule::createWithXmlProcessorAsDefault()
                                ->enableJsonRpc()
                                ->enableRss()
                                ->enableRest()
                                ->configure($this->binder);
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubRouter', 'xml'));
        $this->assertTrue($this->binder->getInjector()->hasExplicitBinding('stubRouteReader'));
        $processorResolver = $this->binder->getInjector()->getInstance('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver');
        $this->assertInstanceOf('stubDefaultProcessorResolver', $processorResolver);
        $this->assertEquals('xml',
                            DefaultProcessorResolverPropertyAccessor::getDefaultProcessorParamValue($processorResolver)
        );
        $this->assertEquals(array('xml'     => 'net::stubbles::websites::xml::stubXMLProcessor',
                                  'jsonrpc' => 'net::stubbles::service::jsonrpc::stubJsonRpcProcessor',
                                  'rss'     => 'net::stubbles::xml::rss::stubRSSProcessor',
                                  'rest'    => 'net::stubbles::service::rest::stubRestProcessor'
                            ),
                            DefaultProcessorResolverPropertyAccessor::getProcessors($processorResolver)
        );
        $this->assertEquals(array('xml'     => 'interceptors',
                                  'jsonrpc' => 'interceptors-jsonrpc',
                                  'rss'     => 'interceptors-rss',
                                  'rest'    => 'interceptors-rest'
                            ),
                            DefaultProcessorResolverPropertyAccessor::getInterceptorDescriptors($processorResolver)
        );
    }

    /**
     * create a binding module with xml processor as default and enabled jsonrpc and rss processor
     *
     * @test
     */
    public function createWithXmlProcessorAsDefaultWithDifferentInterceptors()
    {
        stubWebsiteBindingModule::createWithXmlProcessorAsDefault('interceptors-foo', 'org::stubbles::test::TestRouter')
                                ->enableJsonRpc('interceptors-bar')
                                ->enableRss('interceptors-baz')
                                ->enableRest('interceptors-aha')
                                ->configure($this->binder);
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubRouter', 'xml'));
        $this->assertTrue($this->binder->getInjector()->hasExplicitBinding('stubRouteReader'));
        $processorResolver = $this->binder->getInjector()->getInstance('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver');
        $this->assertInstanceOf('stubDefaultProcessorResolver', $processorResolver);
        $this->assertEquals('xml',
                            DefaultProcessorResolverPropertyAccessor::getDefaultProcessorParamValue($processorResolver)
        );
        $this->assertEquals(array('xml'     => 'net::stubbles::websites::xml::stubXMLProcessor',
                                  'jsonrpc' => 'net::stubbles::service::jsonrpc::stubJsonRpcProcessor',
                                  'rss'     => 'net::stubbles::xml::rss::stubRSSProcessor',
                                  'rest'    => 'net::stubbles::service::rest::stubRestProcessor'
                            ),
                            DefaultProcessorResolverPropertyAccessor::getProcessors($processorResolver)
        );
        $this->assertEquals(array('xml'     => 'interceptors-foo',
                                  'jsonrpc' => 'interceptors-bar',
                                  'rss'     => 'interceptors-baz',
                                  'rest'    => 'interceptors-aha'
                            ),
                            DefaultProcessorResolverPropertyAccessor::getInterceptorDescriptors($processorResolver)
        );
    }

    /**
     * @test
     */
    public function createWithRestProcessorAsDefault()
    {
        stubWebsiteBindingModule::createWithRestProcessorAsDefault()
                                ->configure($this->binder);
        $processorResolver = $this->binder->getInjector()->getInstance('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver');
        $this->assertInstanceOf('stubSimpleProcessorResolver', $processorResolver);
        $this->assertEquals('net::stubbles::service::rest::stubRestProcessor',
                            SimpleProcessorResolverPropertyAccessor::getProcessorClassName($processorResolver)
        );
        $this->assertEquals('interceptors',
                            $processorResolver->getInterceptorDescriptor($this->getMock('stubRequest'))
        );
    }

    /**
     * @test
     */
    public function createWithRestProcessorAsDefaultDifferentInterceptors()
    {
        stubWebsiteBindingModule::createWithRestProcessorAsDefault('interceptors-aha')
                                ->configure($this->binder);
        $processorResolver = $this->binder->getInjector()->getInstance('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver');
        $this->assertInstanceOf('stubSimpleProcessorResolver', $processorResolver);
        $this->assertEquals('net::stubbles::service::rest::stubRestProcessor',
                            SimpleProcessorResolverPropertyAccessor::getProcessorClassName($processorResolver)
        );
        $this->assertEquals('interceptors-aha',
                            $processorResolver->getInterceptorDescriptor($this->getMock('stubRequest'))
        );
    }

    /**
     * create processor resolver with user-defined processor
     *
     * @test
     */
    public function createUserDefined()
    {
        $this->websiteBindingModule->configure($this->binder);
        $processorResolver = $this->binder->getInjector()->getInstance('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver');
        
        $this->assertInstanceOf('stubSimpleProcessorResolver', $processorResolver);
        $this->assertEquals('org::stubbles::test::FooProcessor',
                            SimpleProcessorResolverPropertyAccessor::getProcessorClassName($processorResolver)
        );
        $this->assertEquals('interceptors',
                            $processorResolver->getInterceptorDescriptor($this->getMock('stubRequest'))
        );
        $this->assertFalse($this->binder->getInjector()->hasBinding('stubRouter', 'foo'));
    }

    /**
     * create processor resolver with user-defined processor
     *
     * @test
     */
    public function createUserDefinedWithDifferentInterceptor()
    {
        stubWebsiteBindingModule::create('foo', 'org::stubbles::test::FooProcessor', 'interceptors-foo', 'org::stubbles::test::FooRouter')
                                ->configure($this->binder);
        $processorResolver = $this->binder->getInjector()->getInstance('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver');
        
        $this->assertInstanceOf('stubSimpleProcessorResolver', $processorResolver);
        $this->assertEquals('org::stubbles::test::FooProcessor',
                            SimpleProcessorResolverPropertyAccessor::getProcessorClassName($processorResolver)
        );
        $this->assertEquals('interceptors-foo',
                            $processorResolver->getInterceptorDescriptor($this->getMock('stubRequest'))
        );
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubRouter', 'foo'));
    }

    /**
     * all bindings should be set
     *
     * @test
     */
    public function bindingsWithAuthEnabled()
    {
        $this->binder->bind('stubCacheContainer')->toInstance($this->getMock('stubCacheContainer'));
        $this->binder->bind('stubMode')->toInstance(stubDefaultMode::prod());
        $this->binder->bindConstant()->named('net.stubbles.config.path')->to(stubPathRegistry::getConfigPath());
        $this->websiteBindingModule->enableAuth();
        $this->websiteBindingModule->configure($this->binder);
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubInterceptorInitializer'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver', 'net.stubbles.websites.processor.finalResolver'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver'));
        $this->assertInstanceOf('stubPropertyBasedInterceptorInitializer', $this->binder->getInjector()->getInstance('stubInterceptorInitializer'));
        $this->assertInstanceOf('stubAuthProcessorResolver', $this->binder->getInjector()->getInstance('stubProcessorResolver'));
    }

    /**
     * all bindings should be set
     *
     * @test
     */
    public function bindingsWithCachingMode()
    {
        $this->binder->bind('stubCacheContainer')->toInstance($this->getMock('stubCacheContainer'));
        $this->binder->bind('stubMode')->toInstance(stubDefaultMode::prod());
        $this->binder->bindConstant()->named('net.stubbles.config.path')->to(stubPathRegistry::getConfigPath());
        $this->websiteBindingModule->configure($this->binder);
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubInterceptorInitializer'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver'));
        $this->assertInstanceOf('stubPropertyBasedInterceptorInitializer', $this->binder->getInjector()->getInstance('stubInterceptorInitializer'));
        $this->assertInstanceOf('stubCachingProcessorResolver', $this->binder->getInjector()->getInstance('stubProcessorResolver'));
    }

    /**
     * all bindings should be set
     *
     * @test
     */
    public function bindingsWithNonCachingMode()
    {
        $this->binder->bind('stubCacheContainer')->toInstance($this->getMock('stubCacheContainer'));
        $this->binder->bind('stubMode')->toInstance(stubDefaultMode::dev());
        $this->binder->bindConstant()->named('net.stubbles.config.path')->to(stubPathRegistry::getConfigPath());
        $this->websiteBindingModule->configure($this->binder);
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubInterceptorInitializer'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver'));
        $this->assertInstanceOf('stubPropertyBasedInterceptorInitializer', $this->binder->getInjector()->getInstance('stubInterceptorInitializer'));
        $this->assertInstanceOf('stubSimpleProcessorResolver', $this->binder->getInjector()->getInstance('stubProcessorResolver'));
    }

    /**
     * @test
     */
    public function bindingsWithDifferentInterceptorInitializer()
    {
        $this->binder->bind('stubCacheContainer')->toInstance($this->getMock('stubCacheContainer'));
        $mockInterceptorInitializerClass = get_class($this->getMock('stubInterceptorInitializer'));
        $this->websiteBindingModule->usingInterceptorInitializer($mockInterceptorInitializerClass)
                                   ->configure($this->binder);
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubInterceptorInitializer'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver'));
        $this->assertTrue($this->binder->getInjector()->hasBinding('stubProcessorResolver'));
        $this->assertInstanceOf($mockInterceptorInitializerClass, $this->binder->getInjector()->getInstance('stubInterceptorInitializer'));
    }

    /**
     * bindings with default xml generators
     *
     * @test
     */
    public function bindingsWithDefaultXmlGenerators()
    {
        $this->websiteBindingModule = stubWebsiteBindingModule::createWithXmlProcessorAsDefault();
        $this->websiteBindingModule->configure($this->binder);
        $this->assertEquals(array('net::stubbles::websites::xml::generator::stubSessionXMLGenerator',
                                  'net::stubbles::websites::xml::generator::stubRouteXMLGenerator',
                                  'net::stubbles::websites::xml::generator::stubRequestXMLGenerator',
                                  'net::stubbles::websites::xml::generator::stubModeXMLGenerator',
                                  'net::stubbles::websites::xml::generator::stubVariantListGenerator'
                            ),
                            $this->binder->getInjector()->getInstance(stubConstantBinding::TYPE, 'net.stubbles.webapp.xml.generators')
        );
    }

    /**
     * bindings with added xml generator
     *
     * @test
     */
    public function bindingsWithAddedXmlGenerator()
    {
        $this->websiteBindingModule = stubWebsiteBindingModule::createWithXmlProcessorAsDefault();
        $this->assertSame($this->websiteBindingModule, $this->websiteBindingModule->addXmlGenerator('foo'));
        $this->websiteBindingModule->configure($this->binder);
        $this->assertEquals(array('net::stubbles::websites::xml::generator::stubSessionXMLGenerator',
                                  'net::stubbles::websites::xml::generator::stubRouteXMLGenerator',
                                  'net::stubbles::websites::xml::generator::stubRequestXMLGenerator',
                                  'net::stubbles::websites::xml::generator::stubModeXMLGenerator',
                                  'net::stubbles::websites::xml::generator::stubVariantListGenerator',
                                  'foo'
                            ),
                            $this->binder->getInjector()->getInstance(stubConstantBinding::TYPE, 'net.stubbles.webapp.xml.generators')
        );
    }

    /**
     * bindings with other xml generators
     *
     * @test
     */
    public function bindingsWithOtherXmlGenerators()
    {
        $this->websiteBindingModule = stubWebsiteBindingModule::createWithXmlProcessorAsDefault();
        $this->assertSame($this->websiteBindingModule, $this->websiteBindingModule->setXmlGenerators(array('foo', 'bar', 'baz')));
        $this->websiteBindingModule->configure($this->binder);
        $this->assertEquals(array('foo',
                                  'bar',
                                  'baz'
                            ),
                            $this->binder->getInjector()->getInstance(stubConstantBinding::TYPE, 'net.stubbles.webapp.xml.generators')
        );
    }
}
?>