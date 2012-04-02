<?php
/**
 * Test for net::stubbles::xml::rss::stubRSSFeedGenerator.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @version     $Id: stubRSSFeedGeneratorTestCase.php 2089 2009-02-10 14:39:23Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::rss::stubRSSFeedGenerator');
/**
 * Test for net::stubbles::xml::rss::stubRSSFeedGenerator.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @group       xml
 * @group       xml_rss
 */
class stubRSSFeedGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRSSFeedGenerator
     */
    protected $rssFeedGenerator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rssFeedGenerator = new stubRSSFeedGenerator('test', 'http://stubbles.net/', 'description');
    }

    /**
     * initial properties should be set correct
     *
     * @test
     */
    public function initialProperties()
    {
        $this->assertEquals('test', $this->rssFeedGenerator->getTitle());
        $this->assertEquals('http://stubbles.net/', $this->rssFeedGenerator->getLink());
        $this->assertEquals('description', $this->rssFeedGenerator->getDescription());
    }

    /**
     * item handling should work correct
     *
     * @test
     */
    public function itemHandling()
    {
        $this->assertEquals(0, $this->rssFeedGenerator->countItems());
        $this->assertEquals(array(), $this->rssFeedGenerator->getItems());
        $this->assertFalse($this->rssFeedGenerator->hasItem(0));
        $this->assertFalse($this->rssFeedGenerator->hasItem(1));
        $this->assertNull($this->rssFeedGenerator->getItem(0));
        $this->assertNull($this->rssFeedGenerator->getItem(1));
        $item0 = $this->rssFeedGenerator->addItem('item0', 'link', 'description');
        $this->assertEquals(1, $this->rssFeedGenerator->countItems());
        $this->assertEquals(array($item0), $this->rssFeedGenerator->getItems());
        $this->assertTrue($this->rssFeedGenerator->hasItem(0));
        $this->assertFalse($this->rssFeedGenerator->hasItem(1));
        $this->assertSame($item0, $this->rssFeedGenerator->getItem(0));
        $this->assertNull($this->rssFeedGenerator->getItem(1));
        $item1 = $this->rssFeedGenerator->addItem('item0', 'link', 'description');
        $this->assertEquals(2, $this->rssFeedGenerator->countItems());
        $this->assertEquals(array($item0, $item1), $this->rssFeedGenerator->getItems());
        $this->assertTrue($this->rssFeedGenerator->hasItem(0));
        $this->assertTrue($this->rssFeedGenerator->hasItem(1));
        $this->assertSame($item0, $this->rssFeedGenerator->getItem(0));
        $this->assertSame($item1, $this->rssFeedGenerator->getItem(1));
    }

    /**
     * test that the values are handles as expexted
     *
     * @test
     */
    public function noItemsNoStylesheets()
    {
        $this->assertEquals(0, $this->rssFeedGenerator->countItems());
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlStreamWriter->expects($this->never())->method('writeProcessingInstruction');
        $mockXmlStreamWriter->expects($this->exactly(2))->method('writeStartElement');
        $mockXmlStreamWriter->expects($this->exactly(2))->method('writeEndElement');
        $mockXmlStreamWriter->expects($this->exactly(4))->method('writeElement');
        $this->assertSame($mockXmlStreamWriter, $this->rssFeedGenerator->serialize($mockXmlStreamWriter));
    }

    /**
     * test that the values are handles as expexted
     *
     * @test
     */
    public function noItemsWithStylesheets()
    {
        $this->rssFeedGenerator->appendStylesheet('foo.xsl');
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlStreamWriter->expects($this->once())->method('writeProcessingInstruction');
        $mockXmlStreamWriter->expects($this->exactly(2))->method('writeStartElement');
        $mockXmlStreamWriter->expects($this->exactly(2))->method('writeEndElement');
        $mockXmlStreamWriter->expects($this->exactly(4))->method('writeElement');
        $this->assertSame($mockXmlStreamWriter, $this->rssFeedGenerator->serialize($mockXmlStreamWriter));
    }

    /**
     * test that the values are handles as expexted
     *
     * @test
     */
    public function withItemsNoStylesheets()
    {
        $this->assertEquals(0, $this->rssFeedGenerator->countItems());
        $this->rssFeedGenerator->addItem('foo', 'bar', 'baz');
        $this->assertEquals(1, $this->rssFeedGenerator->countItems());
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlStreamWriter->expects($this->never())->method('writeProcessingInstruction');
        $mockXmlStreamWriter->expects($this->exactly(3))->method('writeStartElement');
        $mockXmlStreamWriter->expects($this->exactly(3))->method('writeEndElement');
        $mockXmlStreamWriter->expects($this->exactly(7))->method('writeElement');
        $this->assertSame($mockXmlStreamWriter, $this->rssFeedGenerator->serialize($mockXmlStreamWriter));
    }

    /**
     * test that optional channel elements are handled as expected
     *
     * @test
     */
    public function withAllChannelElements()
    {
        $this->assertEquals('Stubbles RSSFeedGenerator', $this->rssFeedGenerator->getGenerator());
        $this->rssFeedGenerator->setLocale('en_EN');
        $this->assertEquals('en_EN', $this->rssFeedGenerator->getLocale());
        $this->rssFeedGenerator->setCopyright('(c) 2007 Stubbles Development Team');
        $this->assertEquals('(c) 2007 Stubbles Development Team', $this->rssFeedGenerator->getCopyright());
        $this->rssFeedGenerator->setManagingEditor('mikey');
        $this->assertEquals('nospam@example.com (mikey)', $this->rssFeedGenerator->getManagingEditor());
        $this->rssFeedGenerator->setWebMaster('schst');
        $this->assertEquals('nospam@example.com (schst)', $this->rssFeedGenerator->getWebMaster());
        $this->rssFeedGenerator->setLastBuildDate(50);
        $this->assertEquals('Thu 01 Jan 1970 01:00:50 +0100', $this->rssFeedGenerator->getLastBuildDate());
        $this->rssFeedGenerator->setTimeToLive(60);
        $this->rssFeedGenerator->setImage('http://example.org/example.gif', 'foo');
        
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlStreamWriter->expects($this->never())->method('writeProcessingInstruction');
        $mockXmlStreamWriter->expects($this->exactly(3))->method('writeStartElement');
        $mockXmlStreamWriter->expects($this->exactly(3))->method('writeEndElement');
        $mockXmlStreamWriter->expects($this->exactly(16))->method('writeElement');
        $this->assertSame($mockXmlStreamWriter, $this->rssFeedGenerator->serialize($mockXmlStreamWriter));
    }

    /**
     * test that optional channel elements are handled as expected
     *
     * @test
     */
    public function withAllChannelElementsSecondVersion()
    {
        $this->rssFeedGenerator->setGenerator('test');
        $this->assertEquals('test', $this->rssFeedGenerator->getGenerator());
        $this->rssFeedGenerator->setLocale('en_EN');
        $this->assertEquals('en_EN', $this->rssFeedGenerator->getLocale());
        $this->rssFeedGenerator->setCopyright('� 2007 Stubbles Development Team');
        $this->assertEquals('� 2007 Stubbles Development Team', $this->rssFeedGenerator->getCopyright());
        $this->rssFeedGenerator->setManagingEditor('example@example.org (mikey)');
        $this->assertEquals('example@example.org (mikey)', $this->rssFeedGenerator->getManagingEditor());
        $this->rssFeedGenerator->setWebMaster('example@example.org (schst)');
        $this->assertEquals('example@example.org (schst)', $this->rssFeedGenerator->getWebMaster());
        $this->rssFeedGenerator->setLastBuildDate('2008-05-24');
        $this->assertEquals('Sat 24 May 2008 00:00:00 +0200', $this->rssFeedGenerator->getLastBuildDate());
        $this->rssFeedGenerator->setTimeToLive(60);
        $this->rssFeedGenerator->setImage('http://example.org/example.gif', 'foo');
        
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlStreamWriter->expects($this->never())->method('writeProcessingInstruction');
        $mockXmlStreamWriter->expects($this->exactly(3))->method('writeStartElement');
        $mockXmlStreamWriter->expects($this->exactly(3))->method('writeEndElement');
        $mockXmlStreamWriter->expects($this->exactly(16))->method('writeElement');
        $this->assertSame($mockXmlStreamWriter, $this->rssFeedGenerator->serialize($mockXmlStreamWriter));
    }

    /**
     * assure that invalid dates throw a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidLastBuildDate()
    {
        $this->rssFeedGenerator->setLastBuildDate('foo');
    }

    /**
     * assure that an invalid width throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function imageWidthTooSmall()
    {
        $this->rssFeedGenerator->setImage('http://example.org/example.gif', 'foo', -1);
    }

    /**
     * assure that an invalid width throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function imageWidthTooGreat()
    {
        $this->rssFeedGenerator->setImage('http://example.org/example.gif', 'foo', 145);
    }

    /**
     * assure that an invalid height throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function imageHeightTooSmall()
    {
         $this->rssFeedGenerator->setImage('http://example.org/example.gif', 'foo', 88, -1);
    }

    /**
     * assure that an invalid height throws a stubIllegalArgumentException
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function imageHeightTooGreat()
    {
        $this->rssFeedGenerator->setImage('http://example.org/example.gif', 'foo', 88, 401);
    }
}
?>