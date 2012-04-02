<?php
/**
 * Test for net::stubbles::webapp::stubUriConfigurator.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::webapp::stubUriConfigurator');
/**
 * Test for net::stubbles::webapp::stubUriConfigurator.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 * @group       webapp
 */
class stubUriConfiguratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function constructWithUnknownDefaultNameAndWithoutDefaultClassThrowsIllegalArgumentException()
    {
        new stubUriConfigurator('new');
    }

    /**
     * @test
     */
    public function canConstructWithUnknownDefaultNameAndDefaultClass()
    {
        $uriConfigurator = new stubUriConfigurator('new', 'my::new::ExampleProcessor');
    }

    /**
     * @test
     */
    public function canConstructWithKnownDefaultNameAndWithoutDefaultClass()
    {
        $uriConfigurator = new stubUriConfigurator('jsonrpc');
    }

    /**
     * @test
     */
    public function createWithXmlProcessorAsDefault()
    {
        $this->assertEquals('xml',
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->getConfig()
                                               ->getProcessorName(new stubUriRequest('/'))
        );
    }

    /**
     * @test
     */
    public function createWithRestProcessorAsDefault()
    {
        $this->assertEquals('rest',
                            stubUriConfigurator::createWithRestProcessorAsDefault()
                                               ->getConfig()
                                               ->getProcessorName(new stubUriRequest('/'))
        );
    }

    /**
     * @test
     */
    public function preInterceptAddsPreInterceptorClasses()
    {
        $this->assertEquals(array('my::PreInterceptor', 'other::PreInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->preIntercept('my::PreInterceptor')
                                               ->preIntercept('other::PreInterceptor', '/foo')
                                               ->getConfig()
                                               ->getPreInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function addShowLastXmlPreInterceptorWithOutUriConditionAddsCorrectInterceptorClassWithOutUriCondition()
    {
        $this->assertEquals(array('net::stubbles::webapp::xml::stubShowLastXmlInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->addShowLastXmlPreInterceptor()
                                               ->getConfig()
                                               ->getPreInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function addShowLastXmlPreInterceptorWithUriConditionAddsCorrectInterceptorClassWithOutUriCondition()
    {
        $this->assertEquals(array('net::stubbles::webapp::xml::stubShowLastXmlInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->addShowLastXmlPreInterceptor('/foo')
                                               ->getConfig()
                                               ->getPreInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function addVariantsPreInterceptorWithOutUriConditionAddsCorrectInterceptorClassWithOutUriCondition()
    {
        $this->assertEquals(array('net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->addVariantsPreInterceptor()
                                               ->getConfig()
                                               ->getPreInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function addVariantsPreInterceptorWithUriConditionAddsCorrectInterceptorClassWithOutUriCondition()
    {
        $this->assertEquals(array('net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->addVariantsPreInterceptor('/foo')
                                               ->getConfig()
                                               ->getPreInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function addVariantSwitchPreInterceptorWithOutUriConditionAddsCorrectInterceptorClassWithOutUriCondition()
    {
        $this->assertEquals(array('net::stubbles::webapp::variantmanager::stubVariantSwitchPreInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->addVariantSwitchPreInterceptor()
                                               ->getConfig()
                                               ->getPreInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function addVariantSwitchPreInterceptorWithUriConditionAddsCorrectInterceptorClassWithOutUriCondition()
    {
        $this->assertEquals(array('net::stubbles::webapp::variantmanager::stubVariantSwitchPreInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->addVariantSwitchPreInterceptor('/foo')
                                               ->getConfig()
                                               ->getPreInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function postInterceptAddsPostInterceptorClasses()
    {
        $this->assertEquals(array('my::PostInterceptor', 'other::PostInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->postIntercept('my::PostInterceptor')
                                               ->postIntercept('other::PostInterceptor', '/foo')
                                               ->getConfig()
                                               ->getPostInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function addEtagPostInterceptorWithOutUriConditionAddsCorrectInterceptorClassWithOutUriCondition()
    {
        $this->assertEquals(array('net::stubbles::ipo::interceptors::stubETagPostInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->addEtagPostInterceptor()
                                               ->getConfig()
                                               ->getPostInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     */
    public function addEtagPostInterceptorWithUriConditionAddsCorrectInterceptorClassWithOutUriCondition()
    {
        $this->assertEquals(array('net::stubbles::ipo::interceptors::stubETagPostInterceptor'),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->addEtagPostInterceptor('/foo')
                                               ->getConfig()
                                               ->getPostInterceptors(new stubUriRequest('/foo'))
        );
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function addProcessorWithNullUriConditionThrowsIllegalArgumentException()
    {
        stubUriConfigurator::createWithXmlProcessorAsDefault()
                           ->process('new', null, 'my::new::ExampleProcessor');
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function addProcessorWithEmptyUriConditionThrowsIllegalArgumentException()
    {
        stubUriConfigurator::createWithXmlProcessorAsDefault()
                           ->process('new', '', 'my::new::ExampleProcessor');
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function addUnknownProcessorWithoutProcessorClassThrowsIllegalArgumentException()
    {
        stubUriConfigurator::createWithXmlProcessorAsDefault()
                           ->process('new', '^/new/');
    }

    /**
     * @test
     */
    public function addUnknownProcessorWithProcessorClass()
    {
        $this->assertEquals('new',
                            stubUriConfigurator::createWithRestProcessorAsDefault()
                                               ->process('new', '^/new/', 'my::new::ExampleProcessor')
                                               ->getConfig()
                                               ->getProcessorName(new stubUriRequest('/new/'))
        );
    }

    /**
     * @test
     */
    public function addKnownProcessorWithoutProcessorClass()
    {
        $this->assertEquals('jsonrpc',
                            stubUriConfigurator::createWithRestProcessorAsDefault()
                                               ->process('jsonrpc', '^/ajax/')
                                               ->getConfig()
                                               ->getProcessorName(new stubUriRequest('/ajax/'))
        );
    }

    /**
     * @test
     */
    public function isProcessorEnabledReturnsTrueIfProcessorWasAdded()
    {
        $this->assertTrue(stubUriConfigurator::createWithXmlProcessorAsDefault()
                                             ->isProcessorEnabled('xml')
        );
    }

    /**
     * @test
     */
    public function isProcessorEnabledReturnsFalseIfProcessorWasNotAdded()
    {
        $this->assertFalse(stubUriConfigurator::createWithXmlProcessorAsDefault()
                                             ->isProcessorEnabled('rest')
        );
    }

    /**
     * @test
     */
    public function provideXmlAddsXmlProcessor()
    {
        $this->assertTrue(stubUriConfigurator::createWithRestProcessorAsDefault()
                                             ->provideXml()
                                             ->isProcessorEnabled('xml')
        );
    }

    /**
     * @test
     */
    public function provideRestAddsRestProcessor()
    {
        $this->assertTrue(stubUriConfigurator::createWithXmlProcessorAsDefault()
                                             ->provideRest()
                                             ->isProcessorEnabled('rest')
        );
    }

    /**
     * @test
     */
    public function provideRestWithDifferentUriCondition()
    {
        $this->assertEquals('rest',
                            stubUriConfigurator::createWithRestProcessorAsDefault()
                                               ->provideRest('^/service/')
                                               ->getConfig()
                                               ->getProcessorName(new stubUriRequest('/service/'))
        );
    }

    /**
     * @test
     */
    public function withRestHandlerEnablesRestProcessor()
    {
        $this->assertTrue(stubUriConfigurator::createWithXmlProcessorAsDefault()
                                             ->withRestHandler('foo', 'my::FooRestHandler')
                                             ->isProcessorEnabled('rest')
        );
    }

    /**
     * @test
     */
    public function hasNoRestHandlersByDefault()
    {
        $this->assertEquals(array(),
                            stubUriConfigurator::createWithRestProcessorAsDefault()
                                               ->getRestHandler()
        );
    }

    /**
     * @test
     */
    public function returnsAddedRestHandlers()
    {
        $this->assertEquals(array('foo' => 'my::FooRestHandler',
                                  'bar' => 'my::BarRestHandler'
                            ),
                            stubUriConfigurator::createWithRestProcessorAsDefault()
                                               ->withRestHandler('foo', 'my::FooRestHandler')
                                               ->withRestHandler('bar', 'my::BarRestHandler')
                                               ->getRestHandler()
        );
    }

    /**
     * @test
     */
    public function provideRssAddsRssProcessor()
    {
        $this->assertTrue(stubUriConfigurator::createWithXmlProcessorAsDefault()
                                             ->provideRss()
                                             ->isProcessorEnabled('rss')
        );
    }

    /**
     * @test
     */
    public function provideRssWithDifferentUriCondition()
    {
        $this->assertEquals('rss',
                            stubUriConfigurator::createWithRestProcessorAsDefault()
                                               ->provideRss('^/feed/')
                                               ->getConfig()
                                               ->getProcessorName(new stubUriRequest('/feed/'))
        );
    }

    /**
     * @test
     */
    public function withRssFeedsEnablesRssProcessor()
    {
        $this->assertTrue(stubUriConfigurator::createWithXmlProcessorAsDefault()
                                             ->withRssFeed('foo', 'my::FooRssFeed')
                                             ->isProcessorEnabled('rss')
        );
    }

    /**
     * @test
     */
    public function hasNoRssFeedsByDefault()
    {
        $this->assertEquals(array(),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->getRssFeeds()
        );
    }

    /**
     * @test
     */
    public function returnsAddedRssFeeds()
    {
        $this->assertEquals(array('foo' => 'my::FooRssFeed',
                                  'bar' => 'my::BarRssFeed'
                            ),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->withRssFeed('foo', 'my::FooRssFeed')
                                               ->withRssFeed('bar', 'my::BarRssFeed')
                                               ->getRssFeeds()
        );
    }

    /**
     * @test
     */
    public function provideJsonRpcAddsJsonRpcProcessor()
    {
        $this->assertTrue(stubUriConfigurator::createWithXmlProcessorAsDefault()
                                             ->provideJsonRpc()
                                             ->isProcessorEnabled('jsonrpc')
        );
    }

    /**
     * @test
     */
    public function provideJsonRpcWithDifferentUriCondition()
    {
        $this->assertEquals('jsonrpc',
                            stubUriConfigurator::createWithRestProcessorAsDefault()
                                               ->provideJsonRpc('^/ajax/')
                                               ->getConfig()
                                               ->getProcessorName(new stubUriRequest('/ajax/'))
        );
    }

    /**
     * @test
     */
    public function processorMapContainsStubblesProcessorsIfNoProcessorsAdded()
    {
        $this->assertEquals(array('jsonrpc' => 'net::stubbles::service::jsonrpc::stubJsonRpcProcessor',
                                  'rest'    => 'net::stubbles::service::rest::stubRestProcessor',
                                  'xml'     => 'net::stubbles::webapp::xml::stubXmlProcessor',
                                  'rss'     => 'net::stubbles::xml::rss::stubRSSProcessor'
                            ),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->getProcessorMap()
        );
    }

    /**
     * @test
     */
    public function processorMapContainsAdditionalAddedProcessors()
    {
        $this->assertEquals(array('jsonrpc' => 'net::stubbles::service::jsonrpc::stubJsonRpcProcessor',
                                  'rest'    => 'net::stubbles::service::rest::stubRestProcessor',
                                  'xml'     => 'net::stubbles::webapp::xml::stubXmlProcessor',
                                  'rss'     => 'net::stubbles::xml::rss::stubRSSProcessor',
                                  'new'     => 'my::new::ExampleProcessor'
                            ),
                            stubUriConfigurator::createWithXmlProcessorAsDefault()
                                               ->process('new', '^/new/', 'my::new::ExampleProcessor')
                                               ->getProcessorMap()
        );
    }

    /**
     * @test
     */
    public function processorMapContainsAdditionalDefaultProcessor()
    {
        $this->assertEquals(array('jsonrpc' => 'net::stubbles::service::jsonrpc::stubJsonRpcProcessor',
                                  'rest'    => 'net::stubbles::service::rest::stubRestProcessor',
                                  'xml'     => 'net::stubbles::webapp::xml::stubXmlProcessor',
                                  'rss'     => 'net::stubbles::xml::rss::stubRSSProcessor',
                                  'new'     => 'my::new::ExampleProcessor'
                            ),
                            stubUriConfigurator::create('new', 'my::new::ExampleProcessor')
                                               ->getProcessorMap()
        );
    }
}
?>