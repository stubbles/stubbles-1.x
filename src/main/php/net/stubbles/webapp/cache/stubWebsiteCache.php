<?php
/**
 * Cache for websites.
 * 
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubWebsiteCache.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse'
);
/**
 * Cache for websites.
 * 
 * @package     stubbles
 * @subpackage  webapp_cache
 * @ProvidedBy(net::stubbles::webapp::cache::stubWebsiteCacheProvider.class)
 */
interface stubWebsiteCache extends stubObject
{
    /**
     * adds a variable to the list of cache variables
     *
     * @param   string            $name
     * @param   scalar            $value
     * @return  stubWebsiteCache
     */
    public function addCacheVar($name, $value);

    /**
     * adds a list of variables to the list of cache variables
     *
     * @param   array<string,scalar>  $cacheVars
     * @return  stubWebsiteCache
     */
    public function addCacheVars(array $cacheVars);

    /**
     * retrieves data from cache and puts it into response
     *
     * @param   stubRequest   $request
     * @param   stubResponse  $response
     * @param   string        $routeName  name of the route to be cached
     * @return  bool|string   true if data was retrieved from cache, else the reason for this miss
     */
    public function retrieve(stubRequest $request, stubResponse $response, $routeName);

    /**
     * stores the data from the response in cache
     *
     * @param   stubRequest   $request
     * @param   stubResponse  $response
     * @param   string        $routeName  name of the route to be cached
     * @return  bool          true if successfully stored, else false
     */
    public function store(stubRequest $request, stubResponse $response, $routeName);
}
?>