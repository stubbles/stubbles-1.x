<?php
/**
 * Router implementation to read route information from property files.
 *
 * @package     stubbles
 * @subpackage  websites_processors_routing
 * @version     $Id: stubPropertyBasedRouter.php 3183 2011-09-05 09:59:31Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubModifiableProperties',
                      'net::stubbles::websites::processors::routing::stubRouter'
);
/**
 * Router implementation to read route information from property files.
 *
 * @package     stubbles
 * @subpackage  websites_processors_routing
 * @since       1.3.0
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
class stubPropertyBasedRouter extends stubBaseObject implements stubRouter
{
    /**
     * path to config files
     *
     * @var  string
     */
    protected $routeConfigPath;

    /**
     * constructor
     *
     * @param  string  $cachePath        path to cache files
     * @param  string  $routeConfigPath  path to route config files
     * @Inject
     * @Named('net.stubbles.page.path')
     */
    public function __construct($routeConfigPath)
    {
        $this->routeConfigPath = $routeConfigPath . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR;
    }

    /**
     * routes the current request and returns a route info object
     *
     * The method is allowed to fall back to the index route if the request
     * does not specify any route.
     *
     * @param   stubRequest  $request
     * @return  stubRoute
     */
    public function route(stubRequest $request)
    {
        return $this->reroute($request->readParam('route')
                                      ->ifSatisfiesRegex(stubRouter::ROUTENAME_REGEX, 'index')
               );
    }

    /**
     * reroutes the current request to the route determined by $routeName
     *
     * The return value should be null if no route with given name exists.
     *
     * @param   string     $routeName
     * @return  stubRoute
     */
    public function reroute($routeName)
    {
        $routeSource = $this->routeConfigPath . $routeName . '.ini';
        if (file_exists($routeSource) === false) {
            return null;
        }

        return new stubRoute(stubModifiableProperties::fromFile($routeSource)
                                                     ->setValue('properties', 'name', $routeName)
        );
    }

    /**
     * redirects the current request to a new request
     *
     * @param  stubRequest   $request
     * @param  stubResponse  $response
     * @param  string        $routeName
     */
    public function redirect(stubRequest $request, stubResponse $response, $routeName)
    {
        $host = $request->readHeader('HTTP_HOST')->unsecure();
        $response->addHeader('Location', '//' . $host . '/xml/' . $routeName);
    }
}
?>