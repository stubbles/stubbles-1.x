<?php
/**
 * Serializer delegate to serialize a value as xml fragment.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_delegate
 * @version     $Id: stubXmlSerializerFragmentDelegate.php 2977 2011-02-07 18:55:46Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::serializer::delegate::stubXmlSerializerDelegate');
/**
 * Serializer delegate to serialize a value as xml fragment.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_delegate
 * @since       1.6.0
 */
class stubXmlSerializerFragmentDelegate extends stubBaseObject implements stubXmlSerializerDelegate
{
    /**
     * name of tag
     *
     * @var  string
     */
    protected $tagName;
    /**
     * switch whether to transform line breaks to <br/> or not
     *
     * @var  bool
     */
    protected $transformNewLineToBr;

    /**
     * constructor
     *
     * @param  string  $tagName               name of tag
     * @param  bool    $transformNewLineToBr  switch whether to transform line breaks to <br/> or not
     */
    public function  __construct($tagName, $transformNewLineToBr)
    {
        $this->tagName              = $tagName;
        $this->transformNewLineToBr = $transformNewLineToBr;
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
        if (null != $this->tagName) {
            $xmlWriter->writeStartElement($this->tagName);
            if (empty($value) === false) {
                if (true === $this->transformNewLineToBr) {
                    $value = str_replace('&', '&amp;', nl2br($value));
                }

                $xmlWriter->writeXmlFragment($value);
            }

            $xmlWriter->writeEndElement();
        } elseif (empty($value) === false) {
            $xmlWriter->writeXmlFragment($value);
        }
    }
}
?>