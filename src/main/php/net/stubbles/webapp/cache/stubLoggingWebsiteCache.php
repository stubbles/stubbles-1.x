<?php
/**
 * Decorator for website cache instances which logs hits and misses on retrieval.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubLoggingWebsiteCache.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::stubLogger',
                      'net::stubbles::webapp::cache::stubWebsiteCache'
);
/**
 * Decorator for website cache instances which logs hits and misses on retrieval.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @since       1.7.0
 */
class stubLoggingWebsiteCache extends stubBaseObject implements stubWebsiteCache
{
    /**
     * decorated cache instance
     *
     * @var  stubWebsiteCache
     */
    protected $websiteCache;
    /**
     * logger instance
     *
     * @var  stubLogger
     */
    protected $logger;

    /**
     * constructor
     *
     * @param  stubWebsiteCache  $websiteCache
     * @param  stubLogger        $logger
     * @Inject
     * @Named(stubLogger::LEVEL_INFO)
     */
    public function __construct(stubWebsiteCache $websiteCache, stubLogger $logger)
    {
        $this->websiteCache = $websiteCache;
        $this->logger       = $logger;
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
        $this->websiteCache->addCacheVar($name, $value);
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
        $this->websiteCache->addCacheVars($cacheVars);
        return $this;
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
        $result = $this->websiteCache->retrieve($request, $response, $routeName);
        if (true !== $result) {
            $this->logMiss($routeName, $result);
        } else {
            $this->logHit($routeName);
        }

        return $result;
    }

    /**
     * helper method to log cache acticity
     *
     * @param   string  $routeName  name of route
     * @param   string  $reason     reason for miss
     */
    protected function logMiss($routeName, $reason)
    {
        $this->logger->createLogEntry('cache')
                     ->addData($routeName)
                     ->addData('miss')
                     ->addData($this->websiteCache->getClassName())
                     ->addData($reason)
                     ->log();
    }

    /**
     * helper method to log cache acticity
     *
     * @param   string  $routeName  name of route
     */
    protected function logHit($routeName)
    {
        $this->logger->createLogEntry('cache')
                     ->addData($routeName)
                     ->addData('hit')
                     ->addData($this->websiteCache->getClassName())
                     ->addData('')
                     ->log();
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
        return $this->websiteCache->store($request, $response, $routeName);
    }
}
?>