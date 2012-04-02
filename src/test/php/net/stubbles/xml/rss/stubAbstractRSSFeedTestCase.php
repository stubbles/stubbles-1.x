<?php
/**
 * Test for net::stubbles::xml::rss::stubAbstractRSSFeed.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @version     $Id: stubAbstractRSSFeedTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::rss::stubAbstractRSSFeed',
                      'net::reflection::stubReflectionClass'
);

/**
 * Simple Test dummy implementation of abstract rss feed instance
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 */
class testAbstractRSSFeed extends stubAbstractRSSFeed {
    protected $title       = 'test feed';
    protected $description = 'test feed description';
    protected $copyright   = 'test copyright';

    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable() { }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars() { }

    protected function doCreate(stubRSSFeedGenerator $o){ return $o; }
}

/**
 * Test for net::stubbles::xml::rss::stubAbstractRSSFeed.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @group       xml
 * @group       xml_rss
 */
class stubAbstractRSSFeedTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAbstractRSSFeed
     */
    protected $abstractRssFeed;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest = $this->getMock('stubRequest');
        $this->abstractRssFeed = new testAbstractRSSFeed();
        $this->abstractRssFeed->setRequest($this->mockRequest);
    }

    /**
     * @test
     */
    public function annotationPresent()
    {
        $reflection = new stubReflectionClass('stubAbstractRSSFeed');
        $this->assertTrue($reflection->getMethod('setRequest')->hasAnnotation('Inject'));
        
        $refMethod = $reflection->getMethod('setLocale');
        $this->assertTrue($refMethod->hasAnnotation('Inject'));
        $this->assertTrue($refMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($refMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.locale', $refMethod->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function linkShouldBeCreatedFromRequest()
    {
        $filteringRequestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                               $this->getMock('stubFilterFactory'),
                                                               'SERVER_NAME',
                                                               'example.com'
                                 );
        $this->mockRequest->expects($this->any())
                          ->method('readHeader')
                          ->with($this->equalTo('SERVER_NAME'))
                          ->will($this->returnValue($filteringRequestValue));
        $this->assertEquals('http://example.com/', $this->abstractRssFeed->getLink());
        $this->assertEquals('http://example.com/', $this->abstractRssFeed->getLink());
    }

    /**
     * @test
     */
    public function createShouldCreateTheRssFeedGenerator()
    {
        $filteringRequestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                               $this->getMock('stubFilterFactory'),
                                                               'SERVER_NAME',
                                                               'example.com'
                                 );
        $this->mockRequest->expects($this->any())
                          ->method('readHeader')
                          ->with($this->equalTo('SERVER_NAME'))
                          ->will($this->returnValue($filteringRequestValue));
        $rssFeedGenerator = $this->abstractRssFeed->create();

        $this->assertInstanceOf('stubRSSFeedGenerator',  $rssFeedGenerator);
        $this->assertEquals('en_EN', $rssFeedGenerator->getLocale());

        $this->assertEquals($this->abstractRssFeed->getTitle(), $rssFeedGenerator->getTitle());
        $this->assertEquals($this->abstractRssFeed->getDescription(), $rssFeedGenerator->getDescription());
        $this->assertEquals($this->abstractRssFeed->getCopyright(), $rssFeedGenerator->getCopyright());
    }

    /**
     * @test
     */
    public function createPassThroughGeneratorWithoutModifyRssInformation()
    {
        $rssGenerator = $this->abstractRssFeed->create(new stubRSSFeedGenerator('foo','http://example.org','bar'));

        $this->assertNotEquals('test feed', $rssGenerator->getTitle());
        $this->assertNotEquals('test feed description', $rssGenerator->getDescription());
        $this->assertNotEquals('test copyright', $rssGenerator->getCopyright());
    }

    /**
     * @test
     */
    public function modifyLocale()
    {
        $filteringRequestValue = new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                               $this->getMock('stubFilterFactory'),
                                                               'SERVER_NAME',
                                                               'example.com'
                                 );
        $this->mockRequest->expects($this->any())
                          ->method('readHeader')
                          ->with($this->equalTo('SERVER_NAME'))
                          ->will($this->returnValue($filteringRequestValue));
        $this->assertSame($this->abstractRssFeed, $this->abstractRssFeed->setLocale('de_DE'));
        $this->assertEquals('de_DE', $this->abstractRssFeed->create()->getLocale());
    }
}
?>