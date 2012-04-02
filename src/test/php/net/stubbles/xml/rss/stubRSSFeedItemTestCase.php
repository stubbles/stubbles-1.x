<?php
/**
 * Test for net::stubbles::xml::rss::stubRSSFeedItem.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @version     $Id: stubRSSFeedItemTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::rss::stubRSSFeedItem');
/**
 * Test for net::stubbles::xml::rss::stubRSSFeedItem.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @group       xml
 * @group       xml_rss
 */
class stubRSSFeedItemTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRSSFeedItem
     */
    protected $rssFeedItem1;
    /**
     * instance to test
     *
     * @var  stubRSSFeedItem
     */
    protected $rssFeedItem2;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rssFeedItem1 = stubRSSFeedItem::create('test1', 'http://stubbles.net/', 'description')
                                             ->byAuthor('mikey')
                                             ->inCategory('cat1')
                                             ->inCategory('cat2', 'domain')
                                             ->addCommentsAt('http://stubbles.net/comments/')
                                             ->deliveringEnclosure('http://stubbles.net/enclosure.mp3', 50, 'audio/mpeg')
                                             ->withGuid('dummy')
                                             ->publishedOn(50)
                                             ->inspiredBySource('stubbles', 'http://stubbles.net/source/')
                                             ->withContent('<foo>bar</foo><baz/>')
                               ;
        $this->rssFeedItem2 = stubRSSFeedItem::create('test2', 'http://stubbles.net/', 'description2');
    }

    /**
     * test that the values are handles as expexted
     *
     * @test
     */
    public function values()
    {
        $this->assertEquals('test1', $this->rssFeedItem1->getTitle());
        $this->assertEquals('http://stubbles.net/', $this->rssFeedItem1->getLink());
        $this->assertEquals('description', $this->rssFeedItem1->getDescription());
        $this->assertEquals('nospam@example.com (mikey)', $this->rssFeedItem1->getAuthor());
        $this->assertEquals(array(array('category' => 'cat1',
                                        'domain'   => ''
                                  ),
                                  array('category' => 'cat2',
                                        'domain'   => 'domain'
                                  )
                            ),
                            $this->rssFeedItem1->getCategories()
        );
        $this->assertEquals('http://stubbles.net/comments/', $this->rssFeedItem1->getComments());
        $this->assertEquals(array(array('url'    => 'http://stubbles.net/enclosure.mp3',
                                        'length' => 50,
                                        'type' => 'audio/mpeg'
                                  )
                            ),
                            $this->rssFeedItem1->getEnclosures()
        );
        $this->assertEquals('dummy', $this->rssFeedItem1->getGuid());
        $this->assertTrue($this->rssFeedItem1->isGuidPermaLink());
        $this->assertEquals('Thu 01 Jan 1970 01:00:50 +0100', $this->rssFeedItem1->getPubDate());
        $this->assertEquals(array(array('name' => 'stubbles',
                                        'url'  => 'http://stubbles.net/source/'
                                  )
                            ),
                            $this->rssFeedItem1->getSources()
        );
        
        $this->rssFeedItem1->byAuthor('test@example.net (mikey)');
        $this->assertEquals('test@example.net (mikey)', $this->rssFeedItem1->getAuthor());
        
        $this->rssFeedItem1->withGuid('dummy2', false);
        $this->assertEquals('dummy2', $this->rssFeedItem1->getGuid());
        $this->assertFalse($this->rssFeedItem1->isGuidPermaLink());
    }
    
    /**
     * test that the values are handles as expexted
     *
     * @test
     */
    public function emptyValues()
    {
        $this->assertEquals('test2', $this->rssFeedItem2->getTitle());
        $this->assertEquals('http://stubbles.net/', $this->rssFeedItem2->getLink());
        $this->assertEquals('description2', $this->rssFeedItem2->getDescription());
        $this->assertNull($this->rssFeedItem2->getAuthor());
        $this->assertEquals(array(), $this->rssFeedItem2->getCategories());
        $this->assertNull($this->rssFeedItem2->getComments());
        $this->assertEquals(array(), $this->rssFeedItem2->getEnclosures());
        $this->assertNull($this->rssFeedItem2->getGuid());
        $this->assertNull($this->rssFeedItem2->getPubDate());
        $this->assertEquals(array(), $this->rssFeedItem2->getSources());
        $this->assertEquals('', $this->rssFeedItem2->getContent());
    }
    
    /**
     * testz that serializing the rss item works as expected
     *
     * @test
     */
    public function serialize()
    {
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlStreamWriter->expects($this->once())->method('writeStartElement');
        $mockXmlStreamWriter->expects($this->exactly(12))->method('writeElement');
        $this->rssFeedItem1->serialize($mockXmlStreamWriter);
    }
    
    /**
     * testz that serializing the rss item works as expected
     *
     * @test
     */
    public function emptySerialize()
    {
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlStreamWriter->expects($this->once())->method('writeStartElement');
        $mockXmlStreamWriter->expects($this->exactly(3))->method('writeElement');
        $this->rssFeedItem2->serialize($mockXmlStreamWriter);
    }

    /**
     * alternate publishing date should be valid as well
     *
     * @test
     */
    public function publishingDateAsInstance()
    {
        $date = new stubDate('2008-05-24');
        $this->assertSame($this->rssFeedItem1, $this->rssFeedItem1->publishedOn($date));
        $this->assertEquals('Sat 24 May 2008 00:00:00 +0200', $this->rssFeedItem1->getPubDate());
    }

    /**
     * alternate publishing date should be valid as well
     *
     * @test
     */
    public function alternativePublishingDate()
    {
        $this->assertSame($this->rssFeedItem1, $this->rssFeedItem1->publishedOn('2008-05-24'));
        $this->assertEquals('Sat 24 May 2008 00:00:00 +0200', $this->rssFeedItem1->getPubDate());
    }

    /**
     * assure that invalid dates throw a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidPublishingDate()
    {
        $this->rssFeedItem1->publishedOn('foo');
    }
}
?>