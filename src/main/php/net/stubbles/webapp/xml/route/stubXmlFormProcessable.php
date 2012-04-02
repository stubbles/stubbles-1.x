<?php
/**
 * Interface for a xml form processable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id: stubXmlFormProcessable.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::route::stubProcessable');
/**
 * Interface for a xml form processable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 */
interface stubXmlFormProcessable extends stubProcessable
{
    /**
     * returns a list of form values
     *
     * @return  array<string,string>
     */
    public function getFormValues();
}
?>