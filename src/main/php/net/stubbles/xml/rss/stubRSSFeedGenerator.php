<?php
/**
 * Class for generating a rss 2.0 feed.
 *
 * @package     stubbles
 * @subpackage  xml_rss
 * @version     $Id: stubRSSFeedGenerator.php 2089 2009-02-10 14:39:23Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::xml::rss::stubRSSFeedItem',
                      'net::stubbles::xml::stubXMLStreamWriter'
);
/**
 * Class for generating a rss 2.0 feed.
 *
 * The implementation follows the rss specification available at
 * http://rssboard.org/rss-specification. However some of the elements are
 * not implemented:
 * pubDate
 * category   Why categorize a whole feed when the items can be categorized?
 * cloud      This implies security and spamming dangers.
 * rating
 * textInput  Most aggregators ignore it.
 * skipHours  Usage relies on behaviour of aggregators.
 * skipDays   Usage relies on behaviour of aggregators.
 *
 * @package     stubbles
 * @subpackage  xml_rss
 * @see         http://rssboard.org/rss-specification
 */
class stubRSSFeedGenerator extends stubBaseObject
{
    /**
     * name of the channel
     *
     * @var  string
     */
    protected $title          = '';
    /**
     * URL to the HTML website corresponding to the channel
     *
     * @var  string
     */
    protected $link           = '';
    /**
     * phrase or sentence describing the channel
     *
     * @var  string
     */
    protected $description    = '';
    /**
     * list of items in feed
     *
     * @var  array<stubRssFeedItem>
     */
    protected $items          = array();
    /**
     * the generator of this rss feed
     *
     * @var  string
     */
    protected $generator      = 'Stubbles RSSFeedGenerator';
    /**
     * list of stylesheets to append as processing instructions
     *
     * @var  array<string>
     */
    protected $stylesheets    = array();
    /**
     * the locale the channel is written in
     *
     * @var  string
     * @see  http://rssboard.org/rss-language-codes
     */
    protected $locale         = null;
    /**
     * copyright notice for content in the channel
     *
     * @var  string
     */
    protected $copyright      = null;
    /**
     * email address for person responsible for editorial content
     *
     * @var  string
     */
    protected $managingEditor = null;
    /**
     * email address for person responsible for technical issues relating to channel
     *
     * @var  string
     */
    protected $webMaster      = null;
    /**
     * last time the content of the channel changed
     *
     * @var  string
     */
    protected $lastBuildDate  = null;
    /**
     * URL points to RSS file format documentation
     *
     * A URL that points to the documentation for the format used in the RSS
     * file. It's probably a pointer to this page. It's for people who might
     * stumble across an RSS file on a Web server 25 years from now and wonder
     * what it is.
     *
     * @var  string
     */
    protected $docs           = 'http://rssboard.org/rss-specification';
    /**
     * number of minutes that indicates how long a channel can be cached before refreshing from the source
     *
     * @var  int
     */
    protected $ttl            = null;
    /**
     * specifies a GIF, JPEG or PNG image that can be displayed with the channel
     *
     * @var  array
     */
    protected $image          = array('url'         => '',
                                      'description' => '',
                                      'width'       => 88,
                                      'height'      => 31
                                );

    /**
     * constructor
     *
     * @param  string  $title        title of rss feed
     * @param  string  $link         optional  source of rss feed
     * @param  string  $description  optional  source description
     */
    public function __construct($title, $link, $description)
    {
        $this->setTitle($title);
        $this->setLink($link);
        $this->setDescription($description);
    }

    /**
     * sets the rss feed caption
     *
     * @param   string  $title        title of rss feed
     * @return  stubRSSFeedGenerator  provides a fluent interface
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * returns the title of rss feed
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * sets the source of rss feed
     *
     * @param   string  $link         http hyperlink
     * @return  stubRSSFeedGenerator  provides a fluent interface
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * returns the source of rss feed
     *
     * @return  string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * sets the source description
     *
     * @param   string  $description  http hyperlink
     * @return  stubRSSFeedGenerator  provides a fluent interface
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * returns the source description
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * set the locale the channel is written in
     *
     * @param   string                $locale
     * @return  stubRSSFeedGenerator  provides a fluent interface
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * returns the locale
     *
     * @return  string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * set copyright notice for content in the channel
     *
     * @param   string  $copyright
     * @return  stubRSSFeedGenerator  provides a fluent interface
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * returns the copyright notice
     *
     * @return  string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * add an item to the feed
     *
     * @param   string           $title        title of the item
     * @param   string           $link         URL of the item
     * @param   string           $description  item synopsis
     * @return  stubRssFeedItem  the added item
     */
    public function addItem($title, $link, $description)
    {
        return ($this->items[] = stubRssFeedItem::create($title, $link, $description));
    }

    /**
     * adds an entity as item to the rss feed
     *
     * @param   object               $entity
     * @param   array<string,mixed>  $overrides  optional
     * @return  stubRssFeedItem      the item created from $entity
     */
    public function addEntity($entity, array $overrides = array())
    {
        $rssFeedItem = stubRssFeedItem::fromEntity($entity, $overrides);
        array_push($this->items, $rssFeedItem);
        return $rssFeedItem;
    }

    /**
     * checks whether an item is present at given position
     *
     * @param   int   $pos
     * @return  bool
     */
    public function hasItem($pos)
    {
        return isset($this->items[$pos]);
    }

    /**
     * returns item at given position
     *
     * @param   int              $pos
     * @return  stubRssFeedItem
     */
    public function getItem($pos)
    {
        if ($this->hasItem($pos) === true) {
            return $this->items[$pos];
        }

        return null;
    }

    /**
     * returns a list of all items
     *
     * @return  stubRssFeedItem
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * returns the number of items added for this feed
     *
     * @return  int
     */
    public function countItems()
    {
        return count($this->items);
    }

    /**
     * set the generator of the feed
     *
     * @param  string  $generator  name of the generator to use
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * returns the generator of the feed
     *
     * @return  string
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * append a stylesheet to the document
     *
     * @param  string  $stylesheet  the stylesheet to append
     */
    public function appendStylesheet($stylesheet)
    {
        $this->stylesheets[] = $stylesheet;
    }

    /**
     * set email address for person responsible for editorial content
     *
     * @param  string  $managingEditor
     */
    public function setManagingEditor($managingEditor)
    {
        if (strstr($managingEditor, '@') === false) {
            $this->managingEditor = 'nospam@example.com (' . $managingEditor . ')';
        } else {
            $this->managingEditor = $managingEditor;
        }
    }

    /**
     * returns the email address for person responsible for editorial content
     *
     * @return  string
     */
    public function getManagingEditor()
    {
        return $this->managingEditor;
    }

    /**
     * set email address for person responsible for technical issues relating to channel
     *
     * @param  string  $webMaster
     */
    public function setWebMaster($webMaster)
    {
        if (strstr($webMaster, '@') === false) {
            $this->webMaster = 'nospam@example.com (' . $webMaster . ')';
        } else {
            $this->webMaster = $webMaster;
        }
    }

    /**
     * returns the email address for person responsible for technical issues relating to channel
     *
     * @return  string
     */
    public function getWebMaster()
    {
        return $this->webMaster;
    }

    /**
     * set the last time when the content of the channel changed
     *
     * @param   string|int   $lastBuildDate  last time the content of the channel changed
     * @throws  stubIllegalArgumentException
     */
    public function setLastBuildDate($lastBuildDate)
    {
        if (is_int($lastBuildDate) === false) {
            $lastBuildDate = strtotime($lastBuildDate);
            if (false === $lastBuildDate) {
                throw new stubIllegalArgumentException('Argument must be a unix timestamp or a valid string representation of a time.');
            }
        }

        $this->lastBuildDate = date('D d M Y H:i:s O', $lastBuildDate);
    }

    /**
     * returns the last build date
     *
     * @return  string
     */
    public function getLastBuildDate()
    {
        return $this->lastBuildDate;
    }

    /**
     * set number of minutes that indicates how long a channel can be cached
     * before refreshing from the source
     *
     * @param  int  $ttl
     */
    public function setTimeToLive($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * specify a GIF, JPEG or PNG image to be displayed with the channel
     *
     * @param   string  $url          URL of a GIF, JPEG or PNG image that represents the channel
     * @param   string  $description  contains text that is included in the TITLE attribute of the link formed around the image in the HTML rendering
     * @param   int     $width        indicating the width of the image in pixels, must be 0 < $width <= 144, default 88
     * @param   int     $height       indicating the height of the image in pixels, must be 0 < $height <= 400, default 31
     * @throws  stubIllegalArgumentException  in case $width or $height have invalid values
     */
    public function setImage($url, $description, $width = 88, $height = 31)
    {
        if (144 < $width || 0 > $width) {
            throw new stubIllegalArgumentException('Width must be a value between 0 and 144.');
        }

        if (400 < $height || 0 > $height) {
            throw new stubIllegalArgumentException('Height must be a value between 0 and 400.');
        }

        $this->image = array('url'         => $url,
                             'description' => $description,
                             'width'       => $width,
                             'height'      => $height
                       );
    }

    /**
     * serialize the feed to xml and return the given stream writer instance
     *
     * @param   stubXMLStreamWriter  $xmlStreamWriter
     * @return  stubXMLStreamWriter
     */
    public function serialize(stubXMLStreamWriter $xmlStreamWriter)
    {
        foreach ($this->stylesheets as $stylesheet) {
            $xmlStreamWriter->writeProcessingInstruction('xml-stylesheet', 'href="' . $stylesheet . '" type="text/xsl"');
        }

        $xmlStreamWriter->writeStartElement('rss');
        $xmlStreamWriter->writeAttribute('version', '2.0');
        $xmlStreamWriter->writeAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xmlStreamWriter->writeAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');

        $xmlStreamWriter->writeStartElement('channel');
        $xmlStreamWriter->writeElement('title', array(), $this->title);
        $xmlStreamWriter->writeElement('link', array(), $this->link);
        $xmlStreamWriter->writeElement('description', array(), $this->description);
        $xmlStreamWriter->writeElement('generator', array(), $this->generator);

        if (null !== $this->locale) {
            $xmlStreamWriter->writeElement('language', array(), $this->locale);
        }

        if (null !== $this->copyright) {
            $xmlStreamWriter->writeElement('copyright', array(), $this->copyright);
        }

        if (null !== $this->managingEditor) {
            $xmlStreamWriter->writeElement('managingEditor', array(), $this->managingEditor);
        }

        if (null !== $this->webMaster) {
            $xmlStreamWriter->writeElement('webMaster', array(), $this->webMaster);
        }

        if (null !== $this->lastBuildDate) {
            $xmlStreamWriter->writeElement('lastBuildDate', array(), $this->lastBuildDate);
        }

        if (null !== $this->ttl) {
            $xmlStreamWriter->writeElement('ttl', array(), $this->ttl);
        }

        if (strlen($this->image['url']) > 0) {
            $xmlStreamWriter->writeStartElement('image');
            $xmlStreamWriter->writeElement('url', array(), $this->image['url']);
            $xmlStreamWriter->writeElement('title', array(), $this->title);
            $xmlStreamWriter->writeElement('link', array(), $this->link);
            $xmlStreamWriter->writeElement('width', array(), $this->image['width']);
            $xmlStreamWriter->writeElement('height', array(), $this->image['height']);
            $xmlStreamWriter->writeElement('description', array(), $this->image['description']);
            $xmlStreamWriter->writeEndElement();
        }

        foreach ($this->items as $item) {
            $item->serialize($xmlStreamWriter);
        }

        $xmlStreamWriter->writeEndElement(); // end channel element
        $xmlStreamWriter->writeEndElement(); // end rss
        return $xmlStreamWriter;
    }
}
?>