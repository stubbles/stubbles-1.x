<?php
/**
 * Class to transfer the query string into an xml document.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 * @version     $Id: stubXslDateFormatterCallback.php 2098 2009-02-12 22:17:12Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::types::stubDate',
                      'net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::xsl::callback::stubXslAbstractCallback'
);
/**
 * Class to transfer the query string into an xml document.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 */
class stubXslDateFormatterCallback extends stubXslAbstractCallback
{
    /**
     * returns a formatted date
     *
     * If no timestamp is given the current time will be used.
     * 
     * @param   array<DOMAttr>|string  $format     format for the date string to be returned
     * @param   array<DOMAttr>|string  $timestamp  optional  timestamp to format
     * @return  DOMDocument
     * @XslMethod
     */
    public function formatDate($format, $timestamp = null)
    {
        $format    = $this->parseValue($format);
        $timestamp = $this->parseValue($timestamp);
        if (null == $timestamp) {
            $timestamp = time();
        }
        
        $date = new stubDate($timestamp);
        $this->xmlStreamWriter->writeElement('date',
                                             array('timestamp' => $timestamp),
                                             $date->format($format)
        );
        return $this->createDomDocument();
    }

    /**
     * returns a formatted date
     *
     * If no timestamp is given the current time will be used.
     * 
     * @param   array<DOMAttr>|string  $format     format for the date string to be returned
     * @param   array<DOMAttr>|string  $timestamp  optional  timestamp to format
     * @return  DOMDocument
     * @XslMethod
     */
    public function formatLocaleDate($format, $timestamp = null)
    {
        $format    = $this->parseValue($format);
        $timestamp = $this->parseValue($timestamp);
        if (null == $timestamp) {
            $timestamp = time();
        }
        
        $this->xmlStreamWriter->writeElement('date',
                                             array('timestamp' => $timestamp),
                                             strftime($format, $timestamp)
        );
        return $this->createDomDocument();
    }
}
?>