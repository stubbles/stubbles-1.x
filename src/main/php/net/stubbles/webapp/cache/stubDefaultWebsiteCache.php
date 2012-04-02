<?php
/**
 * Default cache implementation for websites.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubDefaultWebsiteCache.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubAbstractWebsiteCache');
/**
 * Default cache implementation for websites.
 * 
 * @package     stubbles
 * @subpackage  webapp_cache
 */
class stubDefaultWebsiteCache extends stubAbstractWebsiteCache
{
    /**
     * prepares the response before it is being stored within the cache
     *
     * @param   stubResponse  $response
     * @return  stubResponse  response to cache
     */
    protected function prepareResponse(stubResponse $response)
    {
        return $response;
    }
}
?>