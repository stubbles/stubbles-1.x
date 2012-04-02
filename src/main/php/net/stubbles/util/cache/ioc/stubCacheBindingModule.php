<?php
/**
 * Binding module for cache containers.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc
 * @version     $Id: stubCacheBindingModule.php 2489 2010-01-25 20:29:28Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule',
                      'net::stubbles::util::cache::stubDefaultCacheStrategy'
);
/**
 * Binding module for cache containers.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc
 */
class stubCacheBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * path to cache directory
     *
     * @var  string
     */
    protected $cachePath;
    /**
     * cache strategy to be used
     *
     * @var  stubCacheStrategy
     */
    protected $cacheStrategy;
    /**
     * provider class for creating the cache container instances
     *
     * @var  string
     */
    protected $cacheContainerProviderClass = 'net::stubbles::util::cache::ioc::stubCacheProvider';
    /**
     * configure values for default cache strategy
     *
     * @var  array<string,double>
     */
    protected $defaultStrategyValues       = array('ttl'           => 86400,
                                                   'maxSize'       => -1,
                                                   'gcProbability' => 10
                                             );

    /**
     * constructor
     *
     * Please note that the cache path is only optional if it is bound by
     * another module.
     *
     * @param  string  $cachePath                    optional  path to cache directory
     * @param  string  $cacheContainerProviderClass  optional  provider implementation which creates cache container instances
     */
    public function __construct($cachePath = null, $cacheContainerProviderClass = null)
    {
        if (null != $cachePath) {
            $this->cachePath = $cachePath;
        }
        
        if (null != $cacheContainerProviderClass) {
            $this->cacheContainerProviderClass = $cacheContainerProviderClass;
        }
    }

    /**
     * static constructor
     *
     * Please note that the cache path is only optional if it is bound by
     * another module.
     *
     * @param   string                  $cachePath                    optional  path to cache directory
     * @param   string                  $cacheContainerProviderClass  optional  provider implementation which creates cache container instances
     * @return  stubCacheBindingModule
     */
    public static function create($cachePath = null, $cacheContainerProviderClass = null)
    {
        return new self($cachePath, $cacheContainerProviderClass);
    }

    /**
     * sets cache strategy to be used
     *
     * @param   stubCacheStrategy       $cacheStrategy
     * @return  stubCacheBindingModule
     */
    public function setCacheStrategy(stubCacheStrategy $cacheStrategy)
    {
        $this->cacheStrategy = $cacheStrategy;
        return $this;
    }

    /**
     * sets config values for default cache strategy
     *
     * @param   int                     $ttl            maximum time to live for cache entries
     * @param   int                     $maxSize        maximum size of cache in bytes (-1 means indefinite)
     * @param   double                  $gcProbability  probability of a garbage collection run between 0 and 1
     * @return  stubCacheBindingModule
     */
    public function setDefaultStrategyValues($ttl, $maxSize, $gcProbability)
    {
        $this->defaultStrategyValues['ttl']           = $ttl;
        $this->defaultStrategyValues['maxSize']       = $maxSize;
        $this->defaultStrategyValues['gcProbability'] = $gcProbability;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        if (null != $this->cachePath) {
            $binder->bindConstant()
                   ->named('net.stubbles.cache.path')
                   ->to($this->cachePath);
        }
        
        $binder->bind('stubCacheStrategy')
               ->toInstance($this->createStrategy());
        $binder->bind('stubCacheContainer')
               ->toProviderClass($this->cacheContainerProviderClass);
    }

    /**
     * creates the cache strategy to be used
     *
     * @return  stubCacheStrategy
     */
    protected function createStrategy()
    {
        if (null !== $this->cacheStrategy) {
            return $this->cacheStrategy;
        }

        $this->cacheStrategy = new stubDefaultCacheStrategy();
        return $this->cacheStrategy->setTimeToLive($this->defaultStrategyValues['ttl'])
                                   ->setMaxCacheSize($this->defaultStrategyValues['maxSize'])
                                   ->setGcProbability($this->defaultStrategyValues['gcProbability']);
    }
}
?>