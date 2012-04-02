<?php
/**
 * Class for a rss 2.0 feed item.
 * 
 * @package     stubbles
 * @subpackage  xml_rss
 * @version     $Id: stubRSSFeedItem.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::types::stubDate',
                      'net::stubbles::xml::stubXMLStreamWriter'
);
/**
 * Class for a rss 2.0 feed item.
 *
 * @package     stubbles
 * @subpackage  xml_rss
 * @see         http://rssboard.org/rss-specification
 */
class stubRSSFeedItem extends stubBaseObject
{
    /**
     * title of the item
     *
     * @var  string
     */
    protected $title       = '';
    /**
     * URL of the item
     *
     * @var  string
     */
    protected $link        = '';
    /**
     * item synopsis
     *
     * @var  string
     */
    protected $description = '';
    /**
     * email address of the author of the item
     *
     * @var  string
     */
    protected $author      = null;
    /**
     * categories where the item is included
     *
     * @var  array
     */
    protected $categories  = array();
    /**
     * URL of a page for comments relating to the item
     *
     * @var  string
     */
    protected $comments    = null;
    /**
     * describes a media object that is attached to the item
     *
     * @var  array
     */
    protected $enclosures  = array();
    /**
     * unique identifier for the item
     *
     * @var  string
     */
    protected $guid        = null;
    /**
     * whether the id may be interpreted as a permanent link or not
     *
     * @var  bool
     */
    protected $isPermaLink = true;
    /**
     * indicates when the item was published
     *
     * @var  string
     */
    protected $pubDate     = null;
    /**
     * where that the item came from
     *
     * @var  array
     */
    protected $sources     = array();
    /**
     * content of rss feed item
     *
     * @var  string
     */
    protected $content     = null;

    /**
     * constructor
     *
     * @param  string  $title        title of the item
     * @param  string  $link         URL of the item
     * @param  string  $description  item synopsis
     */
    private function __construct($title, $link, $description)
    {
        $this->title       = $title;
        $this->link        = $link;
        $this->description = $description;
    }

    /**
     * create a new stubRssFeedItem
     *
     * @param   string           $title        title of the item
     * @param   string           $link         URL of the item
     * @param   string           $description  item synopsis
     * @return  stubRSSFeedItem
     */
    public static function create($title, $link, $description)
    {
        $self = new self($title, $link, $description);
        return $self;
    }

    /**
     * creates a new stubRssFeedItem from given entity
     *
     * @param   object               $entity
     * @param   array<string,mixed>  $overrides  optional
     * @return  stubRSSFeedItem
     * @throws  stubIllegalArgumentException
     * @throws  stubXMLException
     */
    public static function fromEntity($entity, array $overrides = array())
    {
        if (is_object($entity) === false) {
            throw new stubIllegalArgumentException('Given entity must be an object.');
        }
        
        $entityClass = (($entity instanceof stubObject) ? ($entity->getClass()) : (new stubReflectionObject($entity)));
        if ($entityClass->hasAnnotation('RSSFeedItem') === false) {
            throw new stubXMLException('Class ' . $entityClass->getFullQualifiedClassName() . ' is not annotated with @RSSFeedItem.');
        }
        
        $rssFeedItemAnnotation = $entityClass->getAnnotation('RSSFeedItem');
        $self    = new self(self::getRequiredAttribute($entity,
                                                       $entityClass,
                                                       'title',
                                                       $rssFeedItemAnnotation->getTitleMethod('getTitle'),
                                                       $overrides
                            ),
                            self::getRequiredAttribute($entity,
                                                       $entityClass,
                                                       'link',
                                                       $rssFeedItemAnnotation->getLinkMethod('getLink'),
                                                       $overrides
                            ),
                            self::getRequiredAttribute($entity,
                                                       $entityClass,
                                                       'description',
                                                       $rssFeedItemAnnotation->getDescriptionMethod('getDescription'),
                                                       $overrides
                            )
                   );
        
        foreach (array('byAuthor'             => 'getAuthor',
                       'inCategories'         => 'getCategories',
                       'addCommentsAt'        => 'getCommentsURL',
                       'deliveringEnclosures' => 'getEnclosures',
                       'withGuid'             => 'getGuid',
                       'andGuidIsPermaLink'   => 'isPermaLink',
                       'publishedOn'          => 'getPubDate',
                       'inspiredBySources'    => 'getSources',
                       'withContent'          => 'getContent'
                 ) as $itemMethod => $defaultMethod) {
            if (isset($overrides[$itemMethod]) === true) {
                $self->$itemMethod($overrides[$itemMethod]);
                continue;
            }

            if (substr($defaultMethod, 0, 3) === 'get') {
                $annotationMethod = $defaultMethod . 'Method';
            } else {
                $annotationMethod = 'get' . $defaultMethod . 'Method';
            }
            
            $entityMethod     = $rssFeedItemAnnotation->$annotationMethod($defaultMethod);
            if ($entityClass->hasMethod($entityMethod) === true) {
                $self->$itemMethod($entity->$entityMethod());
            }
        }
        
        return $self;
    }

    /**
     * helper method to retrieve a required attribute
     *
     * @param   object                $entity
     * @param   stubReflectionObject  $entityClass
     * @param   string                $name
     * @param   string                $method
     * @param   array<string,mixed>   $overrides
     * @return  string
     * @throws  stubXMLException
     */
    protected static function getRequiredAttribute($entity, $entityClass, $name, $method, array $overrides)
    {
        if (isset($overrides[$name]) === true) {
            return $overrides[$name];
        }

        if ($entityClass->hasMethod($method) === false) {
            throw new stubXMLException('RSSFeedItem ' . $entityClass->getFullQualifiedClassName() . ' does not offer a method to return the ' . $name . ', but ' . $name . ' is required.');
        }

        return $entity->$method();
    }

    /**
     * set the email address of the author of the item who created the item
     *
     * @param   string           $author  author of rss feed item
     * @return  stubRSSFeedItem
     */
    public function byAuthor($author)
    {
        if (strstr($author, '@') === false) {
            $this->author = 'nospam@example.com (' . $author . ')';
        } else {
            $this->author = $author;
        }
        
        return $this;
    }

    /**
     * set one or more categories where the item is included into
     *
     * @param   string  $category  category where the item is included
     * @param   string  $domain    optional  categorization taxonomy
     * @return  stubRSSFeedItem
     */
    public function inCategory($category, $domain = '')
    {
        $this->categories[] = array('category' => $category,
                                    'domain'   => $domain
                              );
        return $this;
    }

    /**
     * sets categories where the item is included into
     *
     * @param   array<string,string>  $categories
     * @return  stubRSSFeedItem
     */
    public function inCategories(array $categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * set the URL of a page for comments relating to the item
     *
     * @param   string  $comments
     * @return  stubRSSFeedItem
     */
    public function addCommentsAt($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * add an enclosure to the item
     *
     * @param   string           $url     location of enclosure
     * @param   int              $length  length of enclosure in bytes
     * @param   string           $type    MIME type of enclosure
     * @return  stubRSSFeedItem
     */
    public function deliveringEnclosure($url, $length, $type)
    {
        $this->enclosures[] = array('url'    => $url,
                                    'length' => $length,
                                    'type'   => $type
                              );
        return $this;
    }

    /**
     * sets enclosures for the item
     *
     * @param   array<array<string,string>>  $enclosures
     * @return  stubRSSFeedItem
     */
    public function deliveringEnclosures(array $enclosures)
    {
        $this->enclosures = $enclosures;
        return $this;
    }

    /**
     * set id of rss feed item
     *
     * @param   string           $guid         the id of the item
     * @param   bool             $isPermaLink  optional
     * @return  stubRSSFeedItem
     */
    public function withGuid($guid, $isPermaLink = true)
    {
        $this->guid        = $guid;
        $this->isPermaLink = $isPermaLink;
        return $this;
    }

    /**
     * sets whether guid is perma link or not
     *
     * @param   bool             $isPermaLink
     * @return  stubRSSFeedItem
     */
    public function andGuidIsPermaLink($isPermaLink)
    {
        $this->isPermaLink = $isPermaLink;
        return $this;
    }

    /**
     * set the date when the item was published
     *
     * @param   string|int|stubDate  $pubDate  publishing date of rss feed item
     * @return  stubRSSFeedItem
     * @throws  stubIllegalArgumentException
     */
    public function publishedOn($pubDate)
    {
        if ($pubDate instanceof stubDate) {
            $pubDate = $pubDate->getTimestamp();
        } elseif (is_int($pubDate) === false) {
            $pubDate = strtotime($pubDate);
            if (false === $pubDate) {
                throw new stubIllegalArgumentException('Argument must be a unix timestamp, a valid string representation of a time or an instance of net::stubbles::lang::types::stubDate.');
            }
        }
        
        $this->pubDate = date('D d M Y H:i:s O', $pubDate);
        return $this;
    }

    /**
     * set the source where that the item came from
     *
     * @param   string           $name  name of the source
     * @param   string           $url   url of the source
     * @return  stubRSSFeedItem
     */
    public function inspiredBySource($name, $url)
    {
        $this->sources[] = array('name' => $name, 'url' => $url);
        return $this;
    }

    /**
     * sets the sources where that the item came from
     *
     * @param   array<array<string,string>>  $sources
     * @return  stubRSSFeedItem
     */
    public function inspiredBySources(array $sources)
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * set the content of the item
     *
     * @param   string           $content  content of rss feed item
     * @return  stubRSSFeedItem
     */
    public function withContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * returns the title of the item
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * returns the URL of the item
     *
     * @return  string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * returns the item synopsis
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * returns the email address of the author of the item
     *
     * @return  string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * returns one or more categories where the item is included into
     *
     * @return  array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * returns the URL of a page for comments relating to the item
     *
     * @return  string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * returns the description of a media object that is attached to the item
     *
     * @return  array
     */
    public function getEnclosures()
    {
        return $this->enclosures;
    }

    /**
     * returns the unique identifier for the item
     *
     * @return  string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * checks whether the guid represents a perma link or not
     *
     * @return  bool
     */
    public function isGuidPermaLink()
    {
        return $this->isPermaLink;
    }

    /**
     * return the publishing date of the item
     *
     * @return  string
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * returns where that the item came from
     *
     * @return  array
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * return the content of the item
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * serialize the item to xml
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter
     */
    public function serialize(stubXMLStreamWriter $xmlStreamWriter)
    {
        $xmlStreamWriter->writeStartElement('item');
        $xmlStreamWriter->writeElement('title', array(), $this->title);
        $xmlStreamWriter->writeElement('link', array(), $this->link);
        $xmlStreamWriter->writeElement('description', array(), $this->description);
        if (null !== $this->author) {
            $xmlStreamWriter->writeElement('author', array(), $this->author);
        }
        
        foreach ($this->categories as $category) {
            $attributes = array();
            if (strlen($category['domain']) > 0) {
                $attributes['domain'] = $category['domain'];
            }
            $xmlStreamWriter->writeElement('category', $attributes, $category['category']);
        }
        
        if (null !== $this->comments) {
            $xmlStreamWriter->writeElement('comments', array(), $this->comments);
        }
        
        foreach ($this->enclosures as $enclosure) {
            $xmlStreamWriter->writeElement('enclosure', array('url'    => $enclosure['url'],
                                                              'length' => $enclosure['length'],
                                                              'type'   => $enclosure['type']
                                                        )
            );
        }
        
        if (null !== $this->guid) {
            $xmlStreamWriter->writeElement('guid', array('isPermaLink' => ((true == $this->isPermaLink) ? ('true') : ('false'))), $this->guid);
        }
        
        if (null !== $this->pubDate) {
            $xmlStreamWriter->writeElement('pubDate', array(), $this->pubDate);
        }
        
        foreach ($this->sources as $source) {
            $xmlStreamWriter->writeElement('source', array('url' => $source['url']), $source['name']);
        }

        if (empty($this->content) === false) {
            $xmlStreamWriter->writeElement('content:encoded', array(), $this->content);
        }
        
        $xmlStreamWriter->writeEndElement(); // end item
    }
}
?>