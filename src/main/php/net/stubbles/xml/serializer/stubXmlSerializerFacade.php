<?php
/**
 * Facade to simplify xml serializing.
 *
 * @package     stubbles
 * @subpackage  xml_serializer
 * @version     $Id: stubXmlSerializerFacade.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::serializer::stubXMLSerializer'
);
/**
 * Facade to simplify xml serializing.
 *
 * @package     stubbles
 * @subpackage  xml_serializer
 * @since       1.1.0
 */
class stubXmlSerializerFacade extends stubBaseObject
{
    /**
     * xml serializer to hide
     *
     * @var  stubXMLSerializer
     */
    protected $xmlSerializer;
    /**
     * xml stream writer to write serialization to
     *
     * @var  stubXMLStreamWriter
     */
    protected $xmlStreamWriter;

    /**
     * constructor
     *
     * @param  stubXMLSerializer    $xmlSerializer
     * @param  stubXMLStreamWriter  $xmlStreamWriter
     * @Inject
     */
    public function __construct(stubXMLSerializer $xmlSerializer, stubXMLStreamWriter $xmlStreamWriter)
    {
        $this->xmlSerializer   = $xmlSerializer;
        $this->xmlStreamWriter = $xmlStreamWriter;
    }
    
    /**
     * serialize any data structure to XML
     *
     * @param   mixed  $data     data to serialize
     * @param   array  $tagName  optional  name for root tag
     * @return  string
     */
    public function serializeToXml($data, $tagName = null)
    {
        return $this->xmlSerializer->serialize($data, $this->xmlStreamWriter, $tagName)
                                   ->asXml();
    }

    /**
     * serialize any data structure to XML
     *
     * @param   mixed        $data     data to serialize
     * @param   array        $tagName  optional  name for root tag
     * @return  DOMDocument
     */
    public function serializeToDom($data, $tagName = null)
    {
        return $this->xmlSerializer->serialize($data, $this->xmlStreamWriter, $tagName)
                                   ->asDOM();
    }
}
?>