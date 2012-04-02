<?php
/**
 * Interface for serializer delegates.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_delegate
 * @version     $Id: stubXmlSerializerDelegate.php 2977 2011-02-07 18:55:46Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::serializer::stubXMLSerializer'
);
/**
 * Interface for serializer delegates.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_delegate
 * @since       1.6.0
 */
interface stubXmlSerializerDelegate extends stubObject
{
    /**
     * serializes given value
     *
     * @param  mixed                $value
     * @param  stubXMLSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  stubXMLStreamWriter  $xmlWriter      xml writer to write serialized object into
     */
    public function serialize($value, stubXMLSerializer $xmlSerializer, stubXMLStreamWriter $xmlWriter);
}
?>