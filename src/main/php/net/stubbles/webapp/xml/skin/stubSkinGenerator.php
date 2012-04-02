<?php
/**
 * Interface to generate the skin to be applied onto the XML result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin
 * @version     $Id: stubSkinGenerator.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubSession');
/**
 * Interface to generate the skin to be applied onto the XML result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin
 */
interface stubSkinGenerator extends stubObject
{
    /**
     * checks whether a given skin exists
     *
     * @param   string  $skinName
     * @return  bool
     */
    public function hasSkin($skinName);

    /**
     * generates the skin document
     *
     * @param   string       $routeName
     * @param   string       $skinName
     * @param   string       $locale
     * @param   string       $processorUri
     * @return  DOMDocument
     */
    public function generate($routeName, $skinName, $locale, $processorUri);
}
?>