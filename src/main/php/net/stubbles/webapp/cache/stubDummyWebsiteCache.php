<?php
/**
 * Dummy implementation collecting cache variables only but not performing any real caching.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubDummyWebsiteCache.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubWebsiteCache');
/**
 * Dummy implementation collecting cache variables only but not performing any real caching.
 *
 * This dummy is useful in test cases where one wants to test if all cache
 * variables are collected, but no real caching should be performed.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 */
class stubDummyWebsiteCache extends stubBaseObject implements stubWebsiteCache
{
    /**
     * list of variables for the cache
     *
     * @var  array<string,scalar>
     */
    protected $cacheVars  = array();

    /**
     * adds a variable to the list of cache variables
     *
     * @param  string  $name
     * @param  scalar  $value
     */
    public function addCacheVar($name, $value)
    {
        $this->cacheVars[$name] = $value;
    }

    /**
     * adds a list of variables to the list of cache variables
     *
     * @param  array<string,scalar>  $cacheVars
     */
    public function addCacheVars(array $cacheVars)
    {
        foreach ($cacheVars as $name => $value) {
            $this->cacheVars[$name] = $value;
        }
    }

    /**
     * returns collected cache variables
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        return $this->cacheVars;
    }

    /**
     * retrieves data from cache and puts it into response
     *
     * @param   stubRequest   $request
     * @param   stubResponse  $response
     * @param   string        $routeName  name of the route to be cached
     * @return  bool          true if data was retrieved from cache, else false
     */
    public function retrieve(stubRequest $request, stubResponse $response, $routeName)
    {
        return false;
    }

    /**
     * stores the data from the response in cche
     *
     * @param   stubRequest   $request
     * @param   stubResponse  $response
     * @param   string        $routeName  name of the route to be cached
     * @return  bool          true if successfully stored, else false
     */
    public function store(stubRequest $request, stubResponse $response, $routeName)
    {
        return false;
    }
}
?>