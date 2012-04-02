<?php
/**
 * Interface for caching strategies.
 *
 * @package     stubbles
 * @subpackage  util_cache
 * @version     $Id: stubCacheStrategy.php 2489 2010-01-25 20:29:28Z mikey $
 */
stubClassLoader::load('net::stubbles::util::cache::stubCacheContainer');
/**
 * Interface for caching strategies.
 *
 * @package     stubbles
 * @subpackage  util_cache
 * @ImplementedBy(net::stubbles::util::cache::stubDefaultCacheStrategy.class)
 */
interface stubCacheStrategy extends stubObject
{
    /**
     * checks whether an item is cacheable or not
     *
     * @param   stubCacheContainer  $container  the container to cache the data in
     * @param   string              $key        the key to cache the data under
     * @param   string              $data       data to cache
     * @return  bool
     */
    public function isCachable(stubCacheContainer $container, $key, $data);

    /**
     * checks whether a cached item is expired
     *
     * @param   stubCacheContainer  $container  the container that contains the cached data
     * @param   string              $key        the key where the data is cached under
     * @return  bool
     */
    public function isExpired(stubCacheContainer $container, $key);

    /**
     * checks whether the garbage collection should be run
     *
     * @param   stubCacheContainer $container
     * @return  bool
     */
    public function shouldRunGc(stubCacheContainer $container);
}
?>