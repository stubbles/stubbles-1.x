<?php
/**
 * Implementation of a website cache which combines several single implementations.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubCompositeWebsiteCache.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubWebsiteCache');
/**
 * Implementation of a website cache which combines several single implementations.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @since       1.7.0
 */
class stubCompositeWebsiteCache extends stubBaseObject implements stubWebsiteCache
{
    /**
     * list of combined website caches
     *
     * @var  array<stubWebsiteCache>
     */
    protected $websiteCaches = array();

    /**
     * adds website cache
     *
     * @param   stubWebsiteCache  $websiteCache
     * @return  stubWebsiteCache
     */
    public function addWebsiteCache(stubWebsiteCache $websiteCache)
    {
        $this->websiteCaches[] = $websiteCache;
        return $websiteCache;
    }

    /**
     * adds a variable to the list of cache variables
     *
     * @param   string            $name
     * @param   scalar            $value
     * @return  stubWebsiteCache
     */
    public function addCacheVar($name, $value)
    {
        foreach ($this->websiteCaches as $websiteCache) {
            $websiteCache->addCacheVar($name, $value);
        }

        return $this;
    }

    /**
     * adds a list of variables to the list of cache variables
     *
     * @param   array<string,scalar>  $cacheVars
     * @return  stubWebsiteCache
     */
    public function addCacheVars(array $cacheVars)
    {
        foreach ($this->websiteCaches as $websiteCache) {
            $websiteCache->addCacheVars($cacheVars);
        }

        return $this;
    }

    /**
     * returns the cache container used by the implementation
     *
     * @return  stubCacheContainer
     */
    public function getCacheContainer()
    {
        ## ?
    }

    /**
     * retrieves data from cache and puts it into response
     *
     * @param   stubRequest   $request
     * @param   stubResponse  $response
     * @param   string        $routeName  name of the route to be cached
     * @return  bool|string   true if data was retrieved from cache, else the reason for this miss
     */
    public function retrieve(stubRequest $request, stubResponse $response, $routeName)
    {
        $missReasons = array();
        foreach ($this->websiteCaches as $websiteCache) {
            $result = $websiteCache->retrieve($request, $response, $routeName);
            if (true === $result) {
                return true;
            }

            $missReasons[] = $websiteCache->getClassName() . ': ' . $result;
        }

        return join(';', $missReasons);
    }

    /**
     * stores the data from the response in cache
     *
     * @param   stubRequest   $request
     * @param   stubResponse  $response
     * @param   string        $routeName  name of the route to be cached
     * @return  bool          true if successfully stored, else false
     */
    public function store(stubRequest $request, stubResponse $response, $routeName)
    {
        $stored = false;
        foreach ($this->websiteCaches as $websiteCache) {
            if ($websiteCache->store($request, $response, $routeName) === true) {
                $stored = true;
            }
        }

        return $stored;
    }
}
?>