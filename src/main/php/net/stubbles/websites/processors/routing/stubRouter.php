<?php
/**
 * Interface for routers.
 *
 * @package     stubbles
 * @subpackage  websites_processors_routing
 * @version     $Id: stubRouter.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::websites::processors::routing::stubRoute'
);
/**
 * Interface for routers.
 *
 * A router is responsible for parsing the request and return a route with
 * informations about what should be processed.
 *
 * @package     stubbles
 * @subpackage  websites_processors_routing
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
interface stubRouter extends stubObject
{
    /**
     * regular expression for checking route names
     */
    const ROUTENAME_REGEX = '/^([a-zA-Z0-9_])+$/';

    /**
     * routes the current request and returns a route info object
     *
     * The method is allowed to fall back to the index route if the request
     * does not specify any route.
     *
     * @param   stubRequest  $request
     * @return  stubRoute
     */
    public function route(stubRequest $request);

    /**
     * reroutes the current request to the route determined by $routeName
     *
     * The return value should be null if no route with given name exists.
     *
     * @param   string     $routeName
     * @return  stubRoute
     */
    public function reroute($routeName);

    /**
     * redirects the current request to a new request
     *
     * @param  stubRequest   $request
     * @param  stubResponse  $response
     * @param  string        $routeName
     */
    public function redirect(stubRequest $request, stubResponse $response, $routeName);
}
?>