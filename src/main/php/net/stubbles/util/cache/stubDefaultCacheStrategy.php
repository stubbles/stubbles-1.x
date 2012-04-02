<?php
/**
 * Default caching strategy.
 *
 * @package     stubbles
 * @subpackage  util_cache
 * @version     $Id: stubDefaultCacheStrategy.php 2492 2010-01-25 21:08:15Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::util::cache::stubCacheContainer'
);
/**
 * Default caching strategy.
 *
 * @package     stubbles
 * @subpackage  util_cache
 */
class stubDefaultCacheStrategy extends stubBaseObject implements stubCacheStrategy
{
    /**
     * time to live for single cached data
     *
     * @var  int
     */
    protected $timeToLive    = 86400;
    /**
     * maximum size of cache
     * 
     * To allow an infinite size set this to -1.
     *
     * @var  string
     */
    protected $maxSize       = -1;
    /**
     * probability of a garbage collection run
     * 
     * Should be a value between 0 and 100 where 0 means never and 100 means always.
     *
     * @var  int
     */
    protected $gcProbability = 10;

    /**
     * sets the time to live for cache entries in seconds
     *
     * @param   int                       $timeToLive
     * @return  stubDefaultCacheStrategy
     * @throws  stubIllegalArgumentException
     * @since   1.1.0
     * @Inject(optional=true)
     * @Named('net.stubbles.util.cache.timeToLive')
     */
    public function setTimeToLive($timeToLive)
    {
        settype($timeToLive, 'integer');
        if (0 > $timeToLive) {
            throw new stubIllegalArgumentException('timeToLive should not be negative');
        }

        $this->timeToLive = $timeToLive;
        return $this;
    }

    /**
     * sets the maximum cache size in bytes
     *
     * Setting the size to -1 means unlimited.
     *
     * @param   int                       $maxSize
     * @return  stubDefaultCacheStrategy
     * @since   1.1.0
     * @Inject(optional=true)
     * @Named('net.stubbles.util.cache.maxSize')
     */
    public function setMaxCacheSize($maxSize)
    {
        $this->maxSize = (int) $maxSize;
        return $this;
    }

    /**
     * sets the probability of a garbage collection run
     *
     * @param   int                       $gcProbability  probability that a garbage collection is run, between 0 and 100
     * @return  stubDefaultCacheStrategy
     * @throws  stubIllegalArgumentException
     * @since   1.1.0
     * @Inject(optional=true)
     * @Named('net.stubbles.util.cache.gcProbability')
     */
    public function setGcProbability($gcProbability)
    {
        settype($gcProbability, 'integer');
        if (0 > $gcProbability || 100 < $gcProbability) {
            throw new stubIllegalArgumentException('gcProbability must be between 0 and 100');
        }

        $this->gcProbability = $gcProbability;
        return $this;
    }

    /**
     * checks whether an item is cacheable or not
     *
     * @param   stubCacheContainer  $container  the container to cache the data in
     * @param   string              $key        the key to cache the data under
     * @param   string              $data       data to cache
     * @return  bool
     */
    public function isCachable(stubCacheContainer $container, $key, $data)
    {
        if (-1 == $this->maxSize) {
            return true;
        }
        
        if (($container->getUsedSpace() + strlen($data) - $container->getSize($key)) > $this->maxSize) {
            return false;
        }
        
        return true;
    }

    /**
     * checks whether a cached item is expired
     *
     * @param   stubCacheContainer  $container  the container that contains the cached data
     * @param   string              $key        the key where the data is cached under
     * @return  bool
     */
    public function isExpired(stubCacheContainer $container, $key)
    {
        return ($container->getLifeTime($key) > $this->timeToLive);
    }

    /**
     * checks whether the garbage collection should be run
     *
     * @param   stubCacheContainer  $container
     * @return  bool
     */
    public function shouldRunGc(stubCacheContainer $container)
    {
        if (rand(1, 100) < $this->gcProbability) {
            return true;
        }
        
        return false;
    }
}
?>