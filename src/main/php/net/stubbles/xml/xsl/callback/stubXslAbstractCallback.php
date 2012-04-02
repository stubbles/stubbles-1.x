<?php
/**
 * Class with helper methods for callbacks.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 * @version     $Id: stubXslAbstractCallback.php 2280 2009-07-28 20:28:58Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubXMLStreamWriter');
/**
 * Class with helper methods for callbacks.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 */
abstract class stubXslAbstractCallback extends stubBaseObject
{
    /**
     * the stream writer to use
     *
     * @var  stubXMLStreamWriter
     */
    protected $xmlStreamWriter;

    /**
     * constructor
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter  xml stream writer to create the document with
     * @Inject
     */
    public function __construct(stubXMLStreamWriter $xmlStreamWriter)
    {
        $this->xmlStreamWriter = $xmlStreamWriter;
    }

    /**
     * parses a value and returns the real value
     *
     * When called from within an xsl stylesheet the given param is often an
     * array with one DOMAttr instance in it. This helper method will return the
     * real value.
     *
     * @param   array<DOMAttr>|string  $value
     * @return  string
     */
    protected function parseValue($value)
    {
        if (is_array($value) === true) {
            if (isset($value[0]) === true && $value[0] instanceof DOMAttr) {
                return $value[0]->value;
            }
            
            return '';
        }

        return $value;
    }

    /**
     * creates DOMDocument and dumps stream writer data from memory
     *
     * @return  DOMDocument
     */
    protected function createDomDocument()
    {
        $doc = $this->xmlStreamWriter->asDom();
        $this->xmlStreamWriter->clear();
        return $doc;
    }
}
?>