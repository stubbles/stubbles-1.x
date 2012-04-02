<?php
/**
 * Abstract base cache implementation for websites.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubAbstractWebsiteCache.php 3294 2011-12-17 23:26:20Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubResponseSerializer',
                      'net::stubbles::util::cache::stubCacheContainer',
                      'net::stubbles::webapp::cache::stubWebsiteCache'
);
/**
 * Abstract base cache implementation for websites.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 */
abstract class stubAbstractWebsiteCache extends stubBaseObject implements stubWebsiteCache
{
    /**
     * list of variables for the cache
     *
     * @var  array<string,scalar>
     */
    protected $cacheVars          = array();
    /**
     * the real cache
     *
     * @var  stubCacheContainer
     */
    protected $cache;
    /**
     * serializer to make the response cachable
     *
     * @var  stubResponseSerializer
     */
    protected $responseSerializer;

    /**
     * constructor
     *
     * @param  stubCacheContainer      $cache
     * @param  stubResponseSerializer  $responseSerializer
     * @Inject
     * @Named{cache}('websites')
     */
    public function __construct(stubCacheContainer $cache, stubResponseSerializer $responseSerializer)
    {
        $this->cache              = $cache;
        $this->responseSerializer = $responseSerializer;
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
        $this->cacheVars[$name] = $value;
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
        foreach ($cacheVars as $name => $value) {
            $this->cacheVars[$name] = $value;
        }

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
        $cacheKey = $this->generateCacheKey($routeName);
        if ($this->cache->has($cacheKey) === false) {
            return 'no cache file';
        }

        try {
            $response->merge($this->responseSerializer->unserialize($this->cache->get($cacheKey)))
                     ->addHeader('X-Cached', $this->getClassName());
            return true;
        } catch (stubException $e) {
            return $e->getMessage();
        }
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
        return (bool) $this->cache->put($this->generateCacheKey($routeName),
                                        $this->responseSerializer->serializeWithoutCookies($this->prepareResponse($response))
                      );
    }

    /**
     * prepares the response before it is being stored within the cache
     *
     * @param   stubResponse  $response
     * @return  stubResponse  response to cache
     */
    protected abstract function prepareResponse(stubResponse $response);

    /**
     * generates the cache key from given list of cache variables
     *
     * @param   string  $routeName  name of the route to be cached
     * @return  string
     */
    protected function generateCacheKey($routeName)
    {
        $cacheKey = $routeName . '?';
        foreach ($this->cacheVars as $name => $value) {
            $cacheKey .= '&' . $name . '=' . $value;
        }

        return md5($cacheKey);
    }
}
?>