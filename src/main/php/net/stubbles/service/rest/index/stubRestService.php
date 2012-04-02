<?php
/**
 * Describes a single rest service.
 *
 * @package     stubbles
 * @subpackage  service_rest_index
 */
stubClassLoader::load('net::stubbles::service::rest::index::stubRestLink');
/**
 * Describes a single rest service.
 *
 * @package     stubbles
 * @subpackage  service_rest_index
 * @since       1.8.0
 * @XMLTag(tagName='service')
 */
class stubRestService extends stubBaseObject
{
    /**
     * uri of this service
     *
     * @var  stubRestLink
     */
    private $link;
    /**
     * name of this service
     *
     * @var  string
     */
    private $name;
    /**
     * description of this service
     *
     * @var  string
     */
    private $description;

    /**
     * constructor
     *
     * @param  stubRestLink  $link
     * @param  string        $name
     * @param  string        $description
     */
    public function __construct(stubRestLink $link, $name, $description)
    {
        $this->link        = $link;
        $this->name        = $name;
        $this->description = $description;
    }

    /**
     * returns uri of the service
     *
     * @XMLTag(tagName='link')
     * @return  stubRestLink
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * returns name of service
     *
     * @XMLAttribute(attributeName='name')
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * returns description of the service
     *
     * @XMLTag(tagName='description')
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
?>