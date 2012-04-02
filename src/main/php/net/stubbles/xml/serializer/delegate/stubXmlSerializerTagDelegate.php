<?php
/**
 * Serializer delegate to serialize a value to a tag.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_delegate
 * @version     $Id: stubXmlSerializerTagDelegate.php 2977 2011-02-07 18:55:46Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::serializer::delegate::stubXmlSerializerDelegate');
/**
 * Serializer delegate to serialize a value to a tag.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_delegate
 * @since       1.6.0
 */
class stubXmlSerializerTagDelegate extends stubBaseObject implements stubXmlSerializerDelegate
{
    /**
     * name of tag
     *
     * @var  string
     */
    protected $tagName;
    /**
     * recurring element tag name for lists
     *
     * @var  string
     */
    protected $elementTagName;

    /**
     * constructor
     *
     * @param  string  $tagName         name of tag
     * @param  string  $elementTagName  optional  recurring element tag name for lists
     */
    public function  __construct($tagName, $elementTagName = null)
    {
        $this->tagName        = $tagName;
        $this->elementTagName = $elementTagName;
    }

    /**
     * serializes given value
     *
     * @param  mixed                $value
     * @param  stubXMLSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  stubXMLStreamWriter  $xmlWriter      xml writer to write serialized object into
     */
    public function serialize($value, stubXMLSerializer $xmlSerializer, stubXMLStreamWriter $xmlWriter)
    {
        $xmlSerializer->serialize($value, $xmlWriter, $this->tagName, $this->elementTagName);
    }
}
?>