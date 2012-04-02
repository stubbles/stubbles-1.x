<?php
/**
 * Test for net::stubbles::xml::rss::stubRSSFeedItemAnnotation.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @version     $Id: stubRSSFeedItemAnnotationTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::rss::stubRSSFeedGenerator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @RSSFeedItem
 */
class MissingAllRssItemEntity extends stubBaseObject
{
    // intentionally empty
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @RSSFeedItem
 */
class MissingLinkAndDescriptionRssItemEntity extends stubBaseObject
{
    /**
     * returns the title
     *
     * @return  string
     */
    public function getTitle()
    {
        return 'simpleTitle';
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @RSSFeedItem
 */
class MissingDescriptionRssItemEntity extends MissingLinkAndDescriptionRssItemEntity
{
    /**
     * returns the link
     *
     * @return  string
     */
    public function getLink()
    {
        return 'simpleLink';
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @RSSFeedItem
 */
class SimpleRssItemEntity extends MissingDescriptionRssItemEntity
{
    /**
     * returns the description
     *
     * @return  string
     */
    public function getDescription()
    {
        return 'simpleDescription';
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @RSSFeedItem
 */
class ExtendedRSSItemEntity extends SimpleRssItemEntity
{
    /**
     * returns the author
     *
     * @return  string
     */
    public function getAuthor()
    {
        return 'extendedAuthor';
    }

    /**
     * returns the categories
     *
     * @return  array<string,string>
     */
    public function getCategories()
    {
        return array('category' => 'extendedCategories',
                     'domain'   => 'extendedDomain'
               );
    }

    /**
     * returns the comments url
     *
     * @return  string
     */
    public function getCommentsURL()
    {
        return 'extendedCommentsURL';
    }

    /**
     * returns the enclosures
     *
     * @return  array<array<string,string>>
     */
    public function getEnclosures()
    {
        return array(array('url'    => 'extendedEnclosureURL',
                           'length' => 'extendedEnclosureLength',
                           'type'   => 'extendedEnclosureType'
                     )
               );
    }

    /**
     * returns the guid
     *
     * @return  string
     */
    public function getGuid()
    {
        return 'extendedGuid';
    }

    /**
     * returns whether guid is perma link or not
     *
     * @return  string
     */
    public function isPermaLink()
    {
        return false;
    }

    /**
     * returns the publishing date
     *
     * @return  string
     */
    public function getPubDate()
    {
        return 1221598221;
    }

    /**
     * returns the sources
     *
     * @return  array<array<string,string>>
     */
    public function getSources()
    {
        return array(array('name' => 'extendedSourceName', 'url' => 'extendedSourceURL'));
    }

    /**
     * returns the content
     *
     * @return  string
     */
    public function getContent()
    {
        return 'extendedContent';
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @RSSFeedItem(titleMethod='getHeadline',
 *              linkMethod='getUrl',
 *              descriptionMethod='getTeaser',
 *              authorMethod='getCreator',
 *              categoriesMethod='getTags',
 *              getCommentsURLMethod='getRemarks',
 *              enclosuresMethod='getImages',
 *              guidMethod='getId',
 *              isPermaLinkMethod='isPermanent',
 *              pubDateMethod='getDate',
 *              sourcesMethod='getOrigin',
 *              contentMethod='getText'
 * )
 */
class RssItemWithDifferentMethods
{
    /**
     * returns the title
     *
     * @return  string
     */
    public function getHeadline()
    {
        return 'headline';
    }

    /**
     * returns the link
     *
     * @return  string
     */
    public function getUrl()
    {
        return 'url';
    }

    /**
     * returns the description
     *
     * @return  string
     */
    public function getTeaser()
    {
        return 'teaser';
    }

    /**
     * returns the author
     *
     * @return  string
     */
    public function getCreator()
    {
        return 'creator@example.com (creator)';
    }

    /**
     * returns the categories
     *
     * @return  array<string,string>
     */
    public function getTags()
    {
        return array('category' => 'tag1',
                     'domain'   => 'other'
               );
    }

    /**
     * returns the comments url
     *
     * @return  string
     */
    public function getRemarks()
    {
        return 'remarks';
    }

    /**
     * returns the enclosures
     *
     * @return  array<array<string,string>>
     */
    public function getImages()
    {
        return array(array('url'    => 'imagesURL',
                           'length' => 'imagesLength',
                           'type'   => 'imagesType'
                     )
               );
    }

    /**
     * returns the guid
     *
     * @return  string
     */
    public function getId()
    {
        return 'id';
    }

    /**
     * returns whether guid is perma link or not
     *
     * @return  string
     */
    public function isPermanent()
    {
        return false;
    }

    /**
     * returns the publishing date
     *
     * @return  string
     */
    public function getDate()
    {
        return 1221598221;
    }

    /**
     * returns the sources
     *
     * @return  array<array<string,string>>
     */
    public function getOrigin()
    {
        return array(array('name' => 'originName', 'url' => 'originURL'));
    }

    /**
     * returns the content
     *
     * @return  string
     */
    public function getText()
    {
        return 'text';
    }
}
/**
 * Test for net::stubbles::xml::rss::stubRSSFeedItemAnnotation.
 *
 * @package     stubbles
 * @subpackage  xml_rss_test
 * @group       xml
 * @group       xml_rss
 */
class stubRSSFeedItemAnnotationTestCase extends PHPUnit_Framework_TestCase
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
        $this->rssFeedGenerator = new stubRSSFeedGenerator('title', 'link', 'description');
    }

    /**
     * no object throws exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function noObject()
    {
        $this->rssFeedGenerator->addEntity(313);
    }

    /**
     * missing annotation throws exception
     *
     * @test
     * @expectedException  stubXMLException
     */
    public function missingAnnotation()
    {
        $this->rssFeedGenerator->addEntity(new stdClass());
    }

    /**
     * missing title throws exception
     *
     * @test
     * @expectedException  stubXMLException
     */
    public function missingTitle()
    {
        $this->rssFeedGenerator->addEntity(new MissingAllRssItemEntity());
    }

    /**
     * missing link throws exception
     *
     * @test
     * @expectedException  stubXMLException
     */
    public function missingLink()
    {
        $this->rssFeedGenerator->addEntity(new MissingLinkAndDescriptionRssItemEntity());
    }

    /**
     * missing description throws exception
     *
     * @test
     * @expectedException  stubXMLException
     */
    public function missingDescription()
    {
        $this->rssFeedGenerator->addEntity(new MissingDescriptionRssItemEntity());
    }

    /**
     * simple entity is transformed into rss item
     *
     * @test
     */
    public function simpleEntity()
    {
        $rssFeedItem = $this->rssFeedGenerator->addEntity(new SimpleRssItemEntity());
        $this->assertEquals('simpleTitle', $rssFeedItem->getTitle());
        $this->assertEquals('simpleLink', $rssFeedItem->getLink());
        $this->assertEquals('simpleDescription', $rssFeedItem->getDescription());
        $this->assertNull($rssFeedItem->getAuthor());
        $this->assertEquals(array(), $rssFeedItem->getCategories());
        $this->assertNull($rssFeedItem->getComments());
        $this->assertEquals(array(), $rssFeedItem->getEnclosures());
        $this->assertNull($rssFeedItem->getGuid());
        $this->assertTrue($rssFeedItem->isGuidPermaLink());
        $this->assertNull($rssFeedItem->getPubDate());
        $this->assertEquals(array(), $rssFeedItem->getSources());
        $this->assertNull($rssFeedItem->getContent());
    }

    /**
     * simple entity is transformed into rss item using overrides
     *
     * @test
     */
    public function simpleEntityWithOverrides()
    {
        $rssFeedItem = $this->rssFeedGenerator->addEntity(new SimpleRssItemEntity(),
                                                          array('title'                => 'overrideTitle',
                                                                'link'                 => 'overrideLink',
                                                                'description'          => 'overrideDescription',
                                                                'byAuthor'             => 'overrideAuthor',
                                                                'inCategories'         => array('category' => 'overrideCategories',
                                                                                                'domain'   => 'overrideDomain'
                                                                                          ),
                                                                'addCommentsAt'        => 'overrideCommentsURL',
                                                                'deliveringEnclosures' => array(array('url'    => 'overrideEnclosureURL',
                                                                                                     'length' => 'overrideEnclosureLength',
                                                                                                     'type'   => 'overrideEnclosureType'
                                                                                                )
                                                                                          ),
                                                                'withGuid'             => 'overrideGuid',
                                                                'andGuidIsPermaLink'   => false,
                                                                'publishedOn'          => 1221598221,
                                                                'inspiredBySources'    => array(array('name' => 'overrideSourceName',
                                                                                                      'url'  => 'overrideSourceURL'
                                                                                                )
                                                                                          ),
                                                                'withContent'          => 'overrideContent'
                                                          )
                       );
        $this->assertEquals('overrideTitle', $rssFeedItem->getTitle());
        $this->assertEquals('overrideLink', $rssFeedItem->getLink());
        $this->assertEquals('overrideDescription', $rssFeedItem->getDescription());
        $this->assertEquals('nospam@example.com (overrideAuthor)', $rssFeedItem->getAuthor());
        $this->assertEquals(array('category' => 'overrideCategories',
                                  'domain'   => 'overrideDomain'
                            ),
                            $rssFeedItem->getCategories()
        );
        $this->assertEquals('overrideCommentsURL', $rssFeedItem->getComments());
        $this->assertEquals(array(array('url'    => 'overrideEnclosureURL',
                                        'length' => 'overrideEnclosureLength',
                                        'type'   => 'overrideEnclosureType'
                                  )
                            ),
                            $rssFeedItem->getEnclosures()
        );
        $this->assertEquals('overrideGuid', $rssFeedItem->getGuid());
        $this->assertFalse($rssFeedItem->isGuidPermaLink());
        $this->assertEquals('Tue 16 Sep 2008 22:50:21 +0200', $rssFeedItem->getPubDate());
        $this->assertEquals(array(array('name' => 'overrideSourceName', 'url' => 'overrideSourceURL')),
                            $rssFeedItem->getSources()
        );
        $this->assertEquals('overrideContent', $rssFeedItem->getContent());
    }

    /**
     * extended entity is transformed into rss item
     *
     * @test
     */
    public function extendedEntity()
    {
        $rssFeedItem = $this->rssFeedGenerator->addEntity(new ExtendedRSSItemEntity());
        $this->assertEquals('simpleTitle', $rssFeedItem->getTitle());
        $this->assertEquals('simpleLink', $rssFeedItem->getLink());
        $this->assertEquals('simpleDescription', $rssFeedItem->getDescription());
        $this->assertEquals('nospam@example.com (extendedAuthor)', $rssFeedItem->getAuthor());
        $this->assertEquals(array('category' => 'extendedCategories',
                                  'domain'   => 'extendedDomain'
                            ),
                            $rssFeedItem->getCategories()
        );
        $this->assertEquals('extendedCommentsURL', $rssFeedItem->getComments());
        $this->assertEquals(array(array('url'    => 'extendedEnclosureURL',
                                        'length' => 'extendedEnclosureLength',
                                        'type'   => 'extendedEnclosureType'
                                  )
                            ),
                            $rssFeedItem->getEnclosures()
        );
        $this->assertEquals('extendedGuid', $rssFeedItem->getGuid());
        $this->assertFalse($rssFeedItem->isGuidPermaLink());
        $this->assertEquals('Tue 16 Sep 2008 22:50:21 +0200', $rssFeedItem->getPubDate());
        $this->assertEquals(array(array('name' => 'extendedSourceName', 'url' => 'extendedSourceURL')),
                            $rssFeedItem->getSources()
        );
        $this->assertEquals('extendedContent', $rssFeedItem->getContent());
    }

    /**
     * different entity is transformed into rss item
     *
     * @test
     */
    public function differentEntity()
    {
        $rssFeedItem = $this->rssFeedGenerator->addEntity(new RssItemWithDifferentMethods());
        $this->assertEquals('headline', $rssFeedItem->getTitle());
        $this->assertEquals('url', $rssFeedItem->getLink());
        $this->assertEquals('teaser', $rssFeedItem->getDescription());
        $this->assertEquals('creator@example.com (creator)', $rssFeedItem->getAuthor());
        $this->assertEquals(array('category' => 'tag1',
                                  'domain'   => 'other'
                            ),
                            $rssFeedItem->getCategories()
        );
        $this->assertEquals('remarks', $rssFeedItem->getComments());
        $this->assertEquals(array(array('url'    => 'imagesURL',
                                        'length' => 'imagesLength',
                                        'type'   => 'imagesType'
                                  )
                            ),
                            $rssFeedItem->getEnclosures()
        );
        $this->assertEquals('id', $rssFeedItem->getGuid());
        $this->assertFalse($rssFeedItem->isGuidPermaLink());
        $this->assertEquals('Tue 16 Sep 2008 22:50:21 +0200', $rssFeedItem->getPubDate());
        $this->assertEquals(array(array('name' => 'originName', 'url' => 'originURL')),
                            $rssFeedItem->getSources()
        );
        $this->assertEquals('text', $rssFeedItem->getContent());
    }
}
?>