<?php
/**
 * Interface for reading xml route configurations.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::webapp::xml::route::stubRoute');
/**
 * Interface for reading xml route configurations.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @since       1.7.0
 */
interface stubRouteReader extends stubObject
{
    /**
     * reads and returns route configuration with given name
     *
     * If the route configuration does not exist it returns null.
     *
     * @param   string     $name
     * @return  stubRoute
     */
    public function getRoute($name);
}
?>