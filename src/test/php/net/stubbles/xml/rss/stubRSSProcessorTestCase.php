<?php
/**
 * Test for net::stubbles::xml::rss::stubRSSProcessor.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @version     $Id: stubRSSProcessorTestCase.php 3206 2011-11-02 16:57:39Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::rss::stubRSSProcessor');
/**
 * Tests for net::stubbles::xml::rss::stubRSSProcessor.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @group       xml
 * @group       xml_rss
 */
class stubRSSProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRSSProcessor
     */
    protected $rssProcessor;
    /**
     * mocked request to use
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked session to use
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
    /**
     * mocked response instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * mocked injector
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest  = $this->getMock('stubRequest');
        $this->mockSession  = $this->getMock('stubSession');
        $this->mockResponse = $this->getMock('stubResponse');
        $this->mockInjector = $this->getMock('stubInjector');
        $this->rssProcessor = new stubRSSProcessor($this->mockRequest,
                                                   $this->mockSession,
                                                   $this->mockResponse,
                                                   $this->mockInjector,
                                                   array('default' => 'org::stubbles::test::xml::rss::DefaultFeed',
                                                         'test'    => 'org::stubbles::test::xml::rss::TestFeed'
                                                   )
                              );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->rssProcessor->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        
        $params = $constructor->getParameters();
        $this->assertTrue($params[4]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.xml.rss.feeds',
                            $params[4]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @expectedException  stubConfigurationException
     */
    public function noFeedsConfiguredThrowsException()
    {
        $rssProcessor = new stubRSSProcessor($this->mockRequest,
                                             $this->mockSession,
                                             $this->mockResponse,
                                             $this->mockInjector,
                                             array()
                        );
    }

    /**
     * @test
     * @expectedException  stubProcessorException
     */
    public function feedNotFoundThrowsProcessorException()
    {
        $this->rssProcessor->startup(new stubUriRequest('/doesNotExist'));
    }

    /**
     * @test
     */
    public function defaultFeed()
    {
        $mockXMLStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXMLStreamWriter->expects($this->once())
                            ->method('asXML')
                            ->will($this->returnValue('rssFeedContents'));
        $mockRssFeedGenerator = $this->getMock('stubRSSFeedGenerator', array(), array('title', 'link', 'description'));
        $mockRssFeedGenerator->expects($this->once())
                             ->method('serialize')
                             ->will($this->returnValue($mockXMLStreamWriter));
        $mockRssFeed  = $this->getMock('stubRSSFeed');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::xml::rss::DefaultFeed'))
                           ->will($this->returnValue($mockRssFeed));
        $this->mockInjector->expects($this->at(1))
                          ->method('getInstance')
                          ->with($this->equalTo('stubXMLStreamWriter'))
                          ->will($this->returnValue($mockXMLStreamWriter));
        $mockRssFeed->expects($this->once())
                    ->method('isCachable')
                    ->will($this->returnValue(false));
        $mockRssFeed->expects($this->once())
                    ->method('getCacheVars')
                    ->will($this->returnValue(array('foo' => 'bar')));
        $mockRssFeed->expects($this->once())
                    ->method('create')
                    ->will($this->returnValue($mockRssFeedGenerator));
        $this->mockResponse->expects($this->once())
                           ->method('addHeader')
                           ->with($this->equalTo('Content-Type'), $this->equalTo('text/xml; charset=utf-8'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('rssFeedContents'));
        $this->assertSame($this->rssProcessor, $this->rssProcessor->startup(new stubUriRequest('/default')));
        $this->assertFalse($this->rssProcessor->isCachable());
        $this->assertEquals(array('foo' => 'bar'), $this->rssProcessor->getCacheVars());
        $this->assertEquals('default', $this->rssProcessor->getRouteName());
        $this->assertSame($this->rssProcessor, $this->rssProcessor->process());
    }

    /**
     * @test
     */
    public function withBinderUsingRequestedFeed()
    {
        $mockXMLStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXMLStreamWriter->expects($this->once())
                            ->method('asXML')
                            ->will($this->returnValue('rssFeedContents'));
        $mockRssFeedGenerator = $this->getMock('stubRSSFeedGenerator', array(), array('title', 'link', 'description'));
        $mockRssFeedGenerator->expects($this->once())
                             ->method('serialize')
                             ->will($this->returnValue($mockXMLStreamWriter));
        $mockRssFeed  = $this->getMock('stubRSSFeed');
        $mockRssFeed->expects($this->once())
                    ->method('isCachable')
                    ->will($this->returnValue(true));
        $mockRssFeed->expects($this->once())
                    ->method('getCacheVars')
                    ->will($this->returnValue(array('foo' => 'bar')));
        $mockRssFeed->expects($this->once())
                    ->method('create')
                    ->will($this->returnValue($mockRssFeedGenerator));
        $this->mockInjector->expects($this->at(0))
                          ->method('getInstance')
                          ->with($this->equalTo('org::stubbles::test::xml::rss::TestFeed'))
                          ->will($this->returnValue($mockRssFeed));
        $this->mockInjector->expects($this->at(1))
                          ->method('getInstance')
                          ->with($this->equalTo('stubXMLStreamWriter'))
                          ->will($this->returnValue($mockXMLStreamWriter));
        $this->mockResponse->expects($this->once())
                           ->method('addHeader')
                           ->with($this->equalTo('Content-Type'), $this->equalTo('text/xml; charset=utf-8'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('rssFeedContents'));
        $this->assertSame($this->rssProcessor, $this->rssProcessor->startup(new stubUriRequest('/test')));
        $this->assertTrue($this->rssProcessor->isCachable());
        $this->assertEquals(array('foo' => 'bar'), $this->rssProcessor->getCacheVars());
        $this->assertEquals('test', $this->rssProcessor->getRouteName());
        $this->assertSame($this->rssProcessor, $this->rssProcessor->process());
    }
}
?>