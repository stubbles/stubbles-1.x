<?php
/**
 * Serializes current mode into xml result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 * @version     $Id: stubModeXmlGenerator.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubMode',
                      'net::stubbles::webapp::xml::generator::stubXmlGenerator'
);
/**
 * Serializes current mode into xml result document.
 *
 * Default values are whether the session is new or not, the current and
 * the next token of the request:
 * <code>
 * <document>
 *   [...]
 *   <mode>
 *     <name>DEV</name>
 *     <isCacheEnabled>true</isCacheEnabled>
 *   </mode>
 *   [...]
 * </document>
 * </code>
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 */
class stubModeXmlGenerator extends stubBaseObject implements stubXmlGenerator
{
    /**
     * name of mode we are running in
     *
     * @var  string
     */
    protected $modeName     = 'PROD';
    /**
     * whether caching is enabled or not
     *
     * @var  bool
     */
    protected $cacheEnabled = true;

    /**
     * sets the mode we are running in
     *
     * @param  stubMode  $mode
     * @Inject(optional=true)
     */
    public function setMode(stubMode $mode)
    {
        $this->modeName     = $mode->name();
        $this->cacheEnabled = $mode->isCacheEnabled();
    }

    /**
     * operations to be done before serialization is done
     */
    public function startup()
    {
        // nothing to do
    }

    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable()
    {
        return $this->cacheEnabled;
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        return array();
    }

    /**
     * serializes session data into result document
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter  writer to be used
     * @param  stubXMLSerializer    $xmlSerializer    serializer to be used
     */
    public function generate(stubXMLStreamWriter $xmlStreamWriter, stubXMLSerializer $xmlSerializer)
    {
        $xmlStreamWriter->writeStartElement('mode');
        $xmlStreamWriter->writeElement('name', array(), $this->modeName);
        $xmlStreamWriter->writeElement('isCacheEnabled', array(), ((true === $this->cacheEnabled) ? ('true') : ('false')));
        $xmlStreamWriter->writeEndElement();  // end mode
    }

    /**
     * operations to be done after serialization is done
     */
    public function cleanup()
    {
        // nothing to do
    }
}
?>
