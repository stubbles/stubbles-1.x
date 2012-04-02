<?php
/**
 * XML STream Writer based on DOM
 *
 * @package     stubbles
 * @subpackage  xml
 * @version     $Id: stubDomXMLStreamWriter.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::stubAbstractXMLStreamWriter'
);
/**
 * XML STream Writer based on DOM
 *
 * @package     stubbles
 * @subpackage  xml
 */
class stubDomXMLStreamWriter extends stubAbstractXMLStreamWriter implements stubXMLStreamWriter
{
    /**
     * List of supported features
     *
     * @var  array
     */
    protected $features = array(stubXMLStreamWriter::FEATURE_AS_DOM,
                                stubXMLStreamWriter::FEATURE_IMPORT_WRITER
                          );
    /**
     * DOM Document
     *
     * @var  DOMDocument
     */
    protected $doc;
    /**
     * Stores al opened elements
     *
     * @var  array
     */
    protected $elementStack = array();

    /**
     * Create a new writer
     *
     * @param  string  $xmlVersion
     * @param  string  $encoding
     */
    public function __construct($xmlVersion = '1.0', $encoding = 'UTF-8')
    {
        $this->xmlVersion = $xmlVersion;
        $this->encoding   = $encoding;
        $this->doc        = new DOMDocument($xmlVersion, $encoding);
    }

    /**
     * Clear all data, that has been written
     */
    public function clear()
    {
        $this->doc = new DOMDocument($this->xmlVersion, $this->encoding);
        $this->elementStack = array();
    }

    /**
     * really writes an opening tag
     *
     * @param   string            $elementName
     * @throws  stubXMLException
     */
    protected function doWriteStartElement($elementName)
    {
        try {
            libxml_use_internal_errors(true);
            $element = $this->doc->createElement($elementName);
            if (count($this->elementStack) == 0) {
                $this->doc->appendChild($element);
            } else {
                $parent = end($this->elementStack);
                $parent->appendChild($element);
            }
            array_push($this->elementStack, $element);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error writing start element: "' . $elementName . '": ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error writing start element "' . $elementName . '".', $e);
        }
    }

    /**
     * Write a text node
     *
     * @param   string            $data
     * @throws  stubXMLException
     */
    public function writeText($data)
    {
        try {
            libxml_use_internal_errors(true);
            $textNode = $this->doc->createTextNode($this->encode($data));
            $this->addToDom($textNode);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error writing text: ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error writing text.', $e);
        }
    }

    /**
     * Write a cdata section
     *
     * @param   string            $cdata
     * @throws  stubXMLException
     */
    public function writeCData($cdata)
    {
        try {
            libxml_use_internal_errors(true);
            $cdataNode = $this->doc->createCDATASection($this->encode($cdata));
            $this->addToDom($cdataNode);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error writing cdata section: ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error writing cdata section.', $e);
        }
    }

    /**
     * Write a comment
     *
     * @param   string            $comment
     * @throws  stubXMLException
     */
    public function writeComment($comment)
    {
        try {
            libxml_use_internal_errors(true);
            $commentNode = $this->doc->createComment($this->encode($comment));
            $this->addToDom($commentNode);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error writing comment: ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error writing comment.', $e);
        }
    }

    /**
     * Write a processing instruction
     *
     * @param   string            $target
     * @param   string            $data
     * @throws  stubXMLException
     */
    public function writeProcessingInstruction($target, $data = '')
    {
        try {
            libxml_use_internal_errors(true);
            $piNode = $this->doc->createProcessingInstruction($target, $data);
            $this->addToDom($piNode);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error writing processing instruction: ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error writing processing instruction.', $e);
        }
    }

    /**
     * Write an xml fragment
     *
     * @param   string            $fragment
     * @throws  stubXMLException
     */
    public function writeXmlFragment($fragment)
    {
        try {
            libxml_use_internal_errors(true);
            $fragmentNode = $this->doc->createDocumentFragment();
            $fragmentNode->appendXML($fragment);
            $this->addToDom($fragmentNode);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error writing document fragment: ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error writing document fragment.', $e);
        }
    }

    /**
     * Write an attribute
     *
     * @param   string            $attributeName
     * @param   string            $attributeValue
     * @throws  stubXMLException
     */
    public function writeAttribute($attributeName, $attributeValue)
    {
        try {
            libxml_use_internal_errors(true);
            $currentElement = end($this->elementStack);
            $currentElement->setAttribute($attributeName, $this->encode($attributeValue));
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error writing attribute:  "' . $attributeName . ':' . $attributeValue . '":' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error writing attribute "' . $attributeName . ':' . $attributeValue . '".', $e);
        }
    }

    /**
     * really writes an end element
     *
     * @throws  stubXMLException
     */
    protected function doWriteEndElement()
    {
        if (count($this->elementStack) === 0) {
            throw new stubXMLException('No open element available.');
        }
        
        array_pop($this->elementStack);
    }

    /**
     * Write a full element
     *
     * @param   string            $elementName
     * @param   array             $attributes  optional
     * @param   string            $cdata       optional
     * @throws  stubXMLException
     */
    public function writeElement($elementName, array $attributes = array(), $cdata = null)
    {
        try {
            libxml_use_internal_errors(true);
            $element = $this->doc->createElement($elementName);
            foreach ($attributes as $attName => $attValue) {
                $element->setAttribute($attName, $this->encode($attValue));
            }
            
            if (null !== $cdata) {
                $element->appendChild($this->doc->createTextNode($cdata));
            }
            
            if (count($this->elementStack) == 0) {
                $this->doc->appendChild($element);
            } else {
                $parent = end($this->elementStack);
                $parent->appendChild($element);
            }
            
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error writing element: "' . $elementName . '":' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error writing element"' . $elementName . '".', $e);
        }
    }

    /**
     * Import another stream
     *
     * @param   stubXMLStreamWriter  $writer
     * @throws  stubXMLException
     */
    public function importStreamWriter(stubXMLStreamWriter $writer)
    {
        try {
            libxml_use_internal_errors(true);
            $newNode = $writer->asDOM()->documentElement;
            $newNodeImported = $this->doc->importNode($newNode, true);
            $this->addToDom($newNodeImported);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new stubXMLException('Error during import: ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (DOMException $e) {
            throw new stubXMLException('Error during import.', $e);
        }
    }

    /**
     * Add a node to the internal DOM tree
     *
     * @param   DOMNode           $node
     * @throws  stubXMLException
     */
    protected function addToDom(DOMNode $node)
    {
        if (count($this->elementStack) < 1) {
            throw new stubXMLException('No tag is currently open, you need to call writeStartElement() first.');
        }
        $current = end($this->elementStack);
        $current->appendChild($node);
    }

    /**
     * Return the XML as a DOM
     *
     * @return  DOMDocument
     */
    public function asDom()
    {
        return $this->doc;
    }

    /**
     * Return the XML as a string
     *
     * @return  string
     */
    public function asXML()
    {
        return rtrim($this->doc->saveXML());
    }

    /**
     * Converts all errors to a string
     *
     * @param   array   $errors
     * @return  string
     */
    protected function convertLibXmlErrorsToString($errors)
    {
        $messages = array();
        foreach ($errors as $error) {
            $messages[] = trim($error->message);
        }
        return implode(', ', $messages);
    }

    /**
     * helper method to transform data into correct encoding
     * 
     * Data has to be encoded even if document encoding is not UTF-8.
     *
     * @param   string  $data
     * @return  string
     * @see     http://php.net/manual/en/function.dom-domdocument-save.php#67952
     */
    protected function encode($data)
    {
        if (mb_detect_encoding($data, 'UTF-8, ISO-8859-1') === 'UTF-8') {
            return $data;
        }
        
        return utf8_encode($data);
    }
}
?>