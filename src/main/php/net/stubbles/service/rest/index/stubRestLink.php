<?php
/**
 * Represents a link.
 *
 * @package     stubbles
 * @subpackage  service_rest_index
 */
/**
 * Represents a link.
 *
 * @package     stubbles
 * @subpackage  service_rest_index
 * @since       1.8.0
 * @XMLTag(tagName='link')
 */
class stubRestLink extends stubBaseObject
{
    /**
     * relation of this uri
     *
     * @var  string
     */
    private $rel;
    /**
     * uri
     *
     * @var  string
     */
    private $uri;

    /**
     * constructor
     *
     * @param  string  $rel
     * @param  string  $uri
     */
    public function __construct($rel, $uri)
    {
        $this->rel = $rel;
        $this->uri = $uri;
    }

    /**
     * returns relation of this uri
     *
     * @XMLAttribute(attributeName='rel')
     * @return  string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * returns uri
     *
     * @XMLAttribute(attributeName='href')
     * @return  string
     */
    public function getUri()
    {
        return $this->uri;
    }
}
?>