<?php
/**
 * Interface for generators of the xml result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 * @version     $Id: stubXmlGenerator.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::serializer::stubXMLSerializer'
);
/**
 * Interface for generators of the xml result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 */
interface stubXmlGenerator extends stubObject
{
    /**
     * operations to be done before serialization is done
     */
    public function startup();

    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable();

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars();

    /**
     * serializes something
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter  writer to be used
     * @param  stubXMLSerializer    $xmlSerializer    serializer to be used
     */
    public function generate(stubXMLStreamWriter $xmlStreamWriter, stubXMLSerializer $xmlSerializer);

    /**
     * operations to be done after serialization is done
     */
    public function cleanup();
}
?>
