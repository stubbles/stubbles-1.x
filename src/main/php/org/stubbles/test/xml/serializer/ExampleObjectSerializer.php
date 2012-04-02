<?php
/**
 * Simple example class to test the xml serializer with an annotated serializer class.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleObjectSerializer.php 2977 2011-02-07 18:55:46Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::serializer::stubObjectXmlSerializer');
/**
 * Simple example class to test the xml serializer with an annotated serializer class.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 */
class ExampleObjectSerializer extends stubBaseObject implements stubObjectXmlSerializer
{
    /**
     * serializes given value
     *
     * @param  mixed                $value
     * @param  stubXMLSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  stubXMLStreamWriter  $xmlWriter      xml writer to write serialized object into
     * @param  string               $tagName        name of the surrounding xml tag
     */
    public function serialize($object, stubXMLSerializer $xmlSerializer, stubXMLStreamWriter $xmlWriter, $tagName)
    {
        if ($object instanceof  ExampleObjectClassWithSerializer) {
            $xmlWriter->writeStartElement('example');
            $xmlWriter->writeAttribute('sound', $object->bar);
            $xmlWriter->writeElement('anything', array(), $object->getSomething());
        }
    }
}
?>