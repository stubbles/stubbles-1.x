<?php
/**
 * Interface for object serializers.
 *
 * @package     stubbles
 * @subpackage  xml_serializer
 * @version     $Id: stubObjectXmlSerializer.php 2977 2011-02-07 18:55:46Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::serializer::stubXMLSerializer'
);
/**
 * Interface for object serializers.
 *
 * @package     stubbles
 * @subpackage  xml_serializer
 * @since       1.6.0
 */
interface stubObjectXmlSerializer extends stubObject
{
    /**
     * serializes given value
     *
     * @param  mixed                $value
     * @param  stubXMLSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  stubXMLStreamWriter  $xmlWriter      xml writer to write serialized object into
     * @param  string               $tagName        name of the surrounding xml tag
     */
    public function serialize($object, stubXMLSerializer $xmlSerializer, stubXMLStreamWriter $xmlWriter, $tagName);
}
?>
