<?php
/**
 * Serializer delegate to serialize a value as attribute.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_delegate
 * @version     $Id: stubXmlSerializerAttributeDelegate.php 2977 2011-02-07 18:55:46Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::serializer::delegate::stubXmlSerializerDelegate');
/**
 * Serializer delegate to serialize a value as attribute.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_delegate
 * @since       1.6.0
 */
class stubXmlSerializerAttributeDelegate extends stubBaseObject implements stubXmlSerializerDelegate
{
    /**
     * name of attribute
     *
     * @var  string
     */
    protected $attributeName;
    /**
     * switch whether to skip serialisation if value is empty
     *
     * @var  bool
     */
    protected $skipEmpty;

    /**
     * constructor
     *
     * @param  string  $attributeName  name of attribute
     * @param  bool    $skipEmpty      switch whether to skip serialisation if value is empty
     */
    public function  __construct($attributeName, $skipEmpty)
    {
        $this->attributeName = $attributeName;
        $this->skipEmpty     = $skipEmpty;
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
        if (gettype($value) === 'boolean') {
            $xmlWriter->writeAttribute($this->attributeName, ((true === $value) ? ('true') : ('false')));
            return;
        }
        
        if ('' === (string) $value && true === $this->skipEmpty) {
            return;
        }

        $xmlWriter->writeAttribute($this->attributeName, (string) $value);
    }
}
?>