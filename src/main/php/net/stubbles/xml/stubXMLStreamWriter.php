<?php
/**
 * Interface to create XML documents
 *
 * @package     stubbles
 * @subpackage  xml
 * @version     $Id: stubXMLStreamWriter.php 2369 2009-10-30 14:54:40Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubXMLException');
/**
 * Interface to create XML documents
 *
 * @package     stubbles
 * @subpackage  xml
 * @ProvidedBy(net::stubbles::xml::stubXmlStreamWriterProvider.class)
 */
interface stubXMLStreamWriter extends stubObject
{
    /**
     * Is able to import an stubXMLStreamWriter
     *
     * @var int
     */
    const FEATURE_IMPORT_WRITER = 1;
    /**
     * Is able to export as DOM
     *
     * @var int
     */
    const FEATURE_AS_DOM = 2;

    /**
     * Create a new writer
     *
     * @param  string  $xmlVersion
     * @param  string  $encoding
     */
    #public function __construct($xmlVersion = '1.0', $encoding = 'UTF-8');

    /**
     * returns the xml version used by the writer
     *
     * @return  string
     */
    public function getVersion();

    /**
     * returns the encoding used by the writer
     *
     * @return  string
     */
    public function getEncoding();

    /**
     * Checks, whether the implementation has a desired feature
     *
     * @param   int  $feature
     * @return  bool
     */
    public function hasFeature($feature);

    /**
     * Clear all data, that has been written
     */
    public function clear();

    /**
     * Write an opening tag
     *
     * @param  string  $elementName
     */
    public function writeStartElement($elementName);

    /**
     * Write a text node
     *
     * @param  string  $data
     */
    public function writeText($data);

    /**
     * Write a cdata section
     *
     * @param  string  $cdata
     */
    public function writeCData($cdata);

    /**
     * Write a comment
     *
     * @param  string  $comment
     */
    public function writeComment($comment);

    /**
     * Write a processing instruction
     *
     * @param  string  $target
     * @param  string  $data
     */
    public function writeProcessingInstruction($target, $data = '');

    /**
     * Write an xml fragment
     *
     * @param  string  $fragment
     */
    public function writeXmlFragment($fragment);

    /**
     * Write an attribute
     *
     * @param  string  $attributeName
     * @param  string  $attributeValue
     */
    public function writeAttribute($attributeName, $attributeValue);

    /**
     * Write an end element
     */
    public function writeEndElement();

    /**
     * Write a full element
     *
     * @param  string  $elementName
     * @param  array   $attributes
     * @param  string  $cdata
     */
    public function writeElement($elementName, array $attributes = array(), $cdata = null);

    /**
     * Import another stream
     *
     * @param  stubXMLStreamWriter  $writer
     */
    public function importStreamWriter(stubXMLStreamWriter $writer);

    /**
     * checks whether the document is finished meaning no open tags are left
     *
     * @return  bool
     */
    public function isFinished();

    /**
     * Return the XML as a string
     *
     * @return  string
     */
    public function asXML();

    /**
     * Return the XML as a DOM
     *
     * @return  DOMDocument
     */
    public function asDOM();
}
?>