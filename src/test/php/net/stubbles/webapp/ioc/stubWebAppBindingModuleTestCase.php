<?php
/**
 * Test for net::stubbles::webapp::ioc::stubWebAppBindingModule.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::webapp::ioc::stubWebAppBindingModule',
                      'net::stubbles::websites::processors::routing::stubRouter'
);
/**
 * Test for net::stubbles::webapp::ioc::stubWebAppBindingModule.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 * @group       webapp
 * @group       webapp_ioc
 */
class stubWebAppBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubWebAppBindingModule
     */
    protected $webAppBindingModule;
    /**
     * mocked uri configurator instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockUriConfigurator;
    /**
     * created uri configuration
     *
     * @var  stubUriConfiguration
     */
    protected $uriConfig;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockUriConfigurator = $this->getMock('stubUriConfigurator', array(), array('example', 'my::ExampleProcessor'));
        $this->webAppBindingModule = stubWebAppBindingModule::create($this->mockUriConfigurator);
        $this->mockUriConfigurator->expects($this->any())
                                  ->method('getProcessorMap')
                                  ->will($this->returnValue(array('example' => 'my::ExampleProcessor')));
        $this->uriConfig = new stubUriConfiguration('example');
        $this->mockUriConfigurator->expects($this->any())
                                  ->method('getConfig')
                                  ->will($this->returnValue($this->uriConfig));
    }

    /**
     * @test
     */
    public function authEnabledIsBoundToFalseByDefault()
    {
        $injector = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasConstant('net.stubbles.webapp.auth'));
        $this->assertFalse($injector->getConstant('net.stubbles.webapp.auth'));
    }

    /**
     * @test
     */
    public function authEnabledIsBoundToTrueIfEnabled()
    {
        $injector = new stubInjector();
        $this->webAppBindingModule->enableAuth()
                                  ->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasConstant('net.stubbles.webapp.auth'));
        $this->assertTrue($injector->getConstant('net.stubbles.webapp.auth'));
    }

    /**
     * @test
     */
    public function processorMapIsBound()
    {
        $injector = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasConstant('net.stubbles.webapp.processor.map'));
        $this->assertEquals(array('example' => 'my::ExampleProcessor'),
                            $injector->getConstant('net.stubbles.webapp.processor.map')
        );
    }

    /**
     * @test
     */
    public function processorIsBound()
    {
        $injector = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasExplicitBinding('stubProcessor'));
    }

    /**
     * @test
     */
    public function uriConfigurationIsBound()
    {
        $injector = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasExplicitBinding('stubUriConfiguration'));
        $this->assertSame($this->uriConfig,
                          $injector->getInstance('stubUriConfiguration')
        );
    }

    /**
     * @test
     */
    public function xmlProcessorDependenciesAreNotBoundIfXmlProcessorNotEnabled()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(false, false, false));
        $injector = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertFalse($injector->hasExplicitBinding('stubRouteReader'));
        $this->assertFalse($injector->hasExplicitBinding('stubSkinGenerator'));
        $this->assertFalse($injector->hasConstant('net.stubbles.webapp.xml.generators'));
    }

    /**
     * @test
     */
    public function xmlProcessorDependenciesAreBoundIfXmlProcessorEnabled()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(true, false, false));
        $injector = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasExplicitBinding('stubRouteReader'));
        $this->assertTrue($injector->hasExplicitBinding('stubSkinGenerator'));
        $this->assertTrue($injector->hasConstant('net.stubbles.webapp.xml.generators'));
    }

    /**
     * @test
     */
    public function xmlGeneratorsAreConfiguredByDefault()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(true, false, false));
        $injector = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasConstant('net.stubbles.webapp.xml.generators'));
        $this->assertEquals(array('net::stubbles::webapp::xml::generator::stubSessionXmlGenerator',
                                  'net::stubbles::webapp::xml::generator::stubRouteXmlGenerator',
                                  'net::stubbles::webapp::xml::generator::stubRequestXmlGenerator',
                                  'net::stubbles::webapp::xml::generator::stubModeXmlGenerator',
                                  'net::stubbles::webapp::xml::generator::stubVariantListGenerator'
                            ),
                            $injector->getConstant('net.stubbles.webapp.xml.generators')
        );
    }

    /**
     * @test
     */
    public function canAddAnotherXmlGenerator()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(true, false, false));
        $injector = new stubInjector();
        $this->webAppBindingModule->addXmlGenerator('my::XmlGenerator')
                                  ->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasConstant('net.stubbles.webapp.xml.generators'));
        $this->assertEquals(array('net::stubbles::webapp::xml::generator::stubSessionXmlGenerator',
                                  'net::stubbles::webapp::xml::generator::stubRouteXmlGenerator',
                                  'net::stubbles::webapp::xml::generator::stubRequestXmlGenerator',
                                  'net::stubbles::webapp::xml::generator::stubModeXmlGenerator',
                                  'net::stubbles::webapp::xml::generator::stubVariantListGenerator',
                                  'my::XmlGenerator'
                            ),
                            $injector->getConstant('net.stubbles.webapp.xml.generators')
        );
    }

    /**
     * @test
     */
    public function bindsRouteReaderToPropertyBasedRouterByDefault()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(true, false, false));
        $injector = new stubInjector();
        $injector->bindConstant()
                 ->named('net.stubbles.cache.path')
                 ->to('');
        $injector->bindConstant()
                 ->named('net.stubbles.page.path')
                 ->to('');
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasExplicitBinding('stubRouteReader'));
        $this->assertInstanceOf('stubPropertyBasedRouteReader',
                                $injector->getInstance('stubRouteReader')
        );
    }

    /**
     * @test
     */
    public function bindsRouteReaderToConfiguredRouterByDefault()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(true, false, false));
        $injector             = new stubInjector();
        $mockRouteReaderClass = get_class($this->getMock('stubRouteReader'));
        $this->webAppBindingModule->setRouteReaderClass($mockRouteReaderClass)
                                  ->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasExplicitBinding('stubRouteReader'));
        $this->assertInstanceOf($mockRouteReaderClass,
                                $injector->getInstance('stubRouteReader')
        );
    }

    /**
     * @test
     */
    public function doesNotBindRestHandlersIfRestProcessorNotEnabled()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(false, false, false));
        $this->mockUriConfigurator->expects($this->never())
                                  ->method('getRestHandler');
        $injector             = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertFalse($injector->hasConstant('net.stubbles.service.rest.handler'));
    }

    /**
     * @test
     */
    public function doesBindRestHandlersIfRestProcessorIsEnabled()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(false, true, false));
        $this->mockUriConfigurator->expects($this->once())
                                  ->method('getRestHandler')
                                  ->will($this->returnValue(array('foo' => 'my::FooRestHandler')));
        $injector             = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasConstant('net.stubbles.service.rest.handler'));
        $this->assertEquals(array('foo' => 'my::FooRestHandler'
                            ),
                            $injector->getConstant('net.stubbles.service.rest.handler')
        );
    }

    /**
     * @test
     */
    public function doesNotBindRssFeedsIfRssProcessorNotEnabled()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(false, false, false));
        $this->mockUriConfigurator->expects($this->never())
                                  ->method('getRssFeeds');
        $injector             = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertFalse($injector->hasConstant('net.stubbles.xml.rss.feeds'));
    }

    /**
     * @test
     */
    public function doesBindRssFeedsIfRssProcessorIsEnabled()
    {
        $this->mockUriConfigurator->expects($this->exactly(3))
                                  ->method('isProcessorEnabled')
                                  ->will($this->onConsecutiveCalls(false, false, true));
        $this->mockUriConfigurator->expects($this->once())
                                  ->method('getRssFeeds')
                                  ->will($this->returnValue(array('foo' => 'my::FooRssFeed')));
        $injector             = new stubInjector();
        $this->webAppBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasConstant('net.stubbles.xml.rss.feeds'));
        $this->assertEquals(array('foo' => 'my::FooRssFeed'
                            ),
                            $injector->getConstant('net.stubbles.xml.rss.feeds')
        );
    }
}
?>
