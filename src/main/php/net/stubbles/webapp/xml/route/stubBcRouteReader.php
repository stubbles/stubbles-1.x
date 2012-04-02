<?php
/**
 * Bc layer for reading xml route configurations when route is selected via request parameter.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::webapp::xml::route::stubRouteReader',
                      'net::stubbles::websites::processors::routing::stubRouter'
);
/**
 * Bc layer for reading xml route configurations when route is selected via request parameter.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @since       1.7.0
 * @deprecated
 */
class stubBcRouteReader extends stubBaseObject implements stubRouteReader
{
    /**
     * old router instance
     *
     * @var  stubRouter
     */
    protected $router;
    /**
     * request instance
     *
     * @var  stubRequest
     */
    protected $request;

    /**
     * constructor
     *
     * @param  stubRouter   $router
     * @param  stubRequest  $request
     * @Inject
     * @Named{router}('xml')
     */
    public function  __construct(stubRouter $router, stubRequest $request)
    {
        $this->router  = $router;
        $this->request = $request;
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
        return $this->router->route($this->request);
    }
}
?>