<?php
/**
 * Abstract base class for XML stream writers.
 *
 * @package     stubbles
 * @subpackage  xml
 * @version     $Id: stubAbstractXMLStreamWriter.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Abstract base class for XML stream writers.
 *
 * @package     stubbles
 * @subpackage  xml
 */
abstract class stubAbstractXMLStreamWriter extends stubBaseObject
{
    /**
     * XML version
     *
     * @var  string
     */
    protected $xmlVersion;
    /**
     * encoding used by the writer
     *
     * @var  string
     */
    protected $encoding;
    /**
     * List of supported features
     *
     * @var  array
     */
    protected $features = array();
    /**
     * depth, i.e. amount of opened tags
     *
     * @var  int
     */
    protected $depth   = 0;

    /**
     * returns the xml version used by the writer
     *
     * @return  string
     */
    public function getVersion()
    {
        return $this->xmlVersion;
    }

    /**
     * returns the encoding used by the writer
     *
     * @return  string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Checks, whether the implementation has a desired feature
     *
     * @param   int   $feature
     * @return  bool
     */
    public function hasFeature($feature)
    {
        return in_array($feature, $this->features);
    }

    /**
     * Write an opening tag
     *
     * @param  string  $elementName
     */
    public function writeStartElement($elementName)
    {
        $this->doWriteStartElement($elementName);
        $this->depth++;
    }

    /**
     * really writes an opening tag
     *
     * @param  string  $elementName
     */
    protected abstract function doWriteStartElement($elementName);

    /**
     * Write an end element
     */
    public function writeEndElement()
    {
        $this->doWriteEndElement();
        $this->depth--;
    }

    /**
     *  really writes an end element
     */
    protected abstract function doWriteEndElement();

    /**
     * checks whether the document is finished meaning no open tags are left
     *
     * @return  bool
     */
    public function isFinished()
    {
        return 0 === $this->depth;
    }
}
?>