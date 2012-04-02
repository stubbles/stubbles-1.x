<?php
/**
 * Interface for reading xml route configurations.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::lang::stubModifiableProperties',
                      'net::stubbles::webapp::xml::route::stubRouteReader'
);
/**
 * Interface for reading xml route configurations.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @since       1.7.0
 */
class stubPropertyBasedRouteReader extends stubBaseObject implements stubRouteReader
{
    /**
     * path to config files
     *
     * @var  string
     */
    protected $routePath;

    /**
     * constructor
     *
     * @param  string  $routePath  path to route config files
     * @Inject
     * @Named('net.stubbles.page.path')
     */
    public function __construct($routePath)
    {
        $this->routePath = $routePath . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR;
    }

    /**
     * reads and returns route configuration with given name
     *
     * If the route configuration does not exist it returns null.
     *
     * @param   string     $name
     * @return  stubRoute
     */
    public function getRoute($name)
    {
        $source = $this->routePath . $name . '.ini';
        if (file_exists($source) === false) {
            return null;
        }

        return new stubRoute(stubModifiableProperties::fromFile($source)
                                                     ->setValue('properties', 'name', $name)
        );
    }
}
?>