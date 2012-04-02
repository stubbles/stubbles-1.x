<?php
/**
 * Gzip cache implementation for websites.
 * 
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubGzipWebsiteCache.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubContainsValidator',
                      'net::stubbles::ipo::response::stubBaseResponse',
                      'net::stubbles::webapp::cache::stubAbstractWebsiteCache'
);
/**
 * Gzip cache implementation for websites.
 * 
 * @package     stubbles
 * @subpackage  webapp_cache
 */
class stubGzipWebsiteCache extends stubAbstractWebsiteCache
{
    /**
     * content header for response
     */
    const HEADER = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
    /**
     * mime type: x-gzip
     */
    const X_GZIP = 'x-gzip';
    /**
     * mime type: gzip
     */
    const GZIP   = 'gzip';

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
        if ($request->acceptsCookies() === false) {
            return 'user agent does not accept cookies';
        }

        $compression = $this->getCompression($request);
        if (null === $compression) {
            return 'user agent does not accept compressed content';
        }

        $result = parent::retrieve($request, $response, $routeName);
        if (true !== $result) {
            return $result;
        }

        $response->addHeader('Content-Encoding', $compression);
        $response->replaceBody(self::HEADER . $response->getBody());
        return true;
    }

    /**
     * helper method to detect the supported compression
     *
     * If null is returned the user agent does not support compression.
     *
     * @param   stubRequest  $request
     * @return  string
     */
    protected function getCompression(stubRequest $request)
    {
        if ($request->validateHeader('HTTP_ACCEPT_ENCODING')->contains(self::X_GZIP) === true) {
            return self::X_GZIP;
        } elseif ($request->validateHeader('HTTP_ACCEPT_ENCODING')->contains(self::GZIP) === true) {
            return self::GZIP;
        }
        
        return null;
    }

    /**
     * prepares the response before it is being stored within the cache
     *
     * @param   stubResponse  $response
     * @return  stubResponse  response to cache
     */
    protected function prepareResponse(stubResponse $response)
    {
        // create another response instance so that origin response does not get modified
        $cacheResponse = new stubBaseResponse($response->getVersion());
        return $cacheResponse->merge($response)
                             ->replaceBody($this->compressBody($response->getBody()));
    }

    /**
     * modifies body
     *
     * @param   string  $body
     * @return  string
     */
    protected function compressBody($original)
    {
        $body       = $this->removeSessionIdentifiers($original);
        $compressed = gzcompress($body, 9);
        return substr($compressed, 0, strlen($compressed) - 4) . $this->convert2Gzip(crc32($body)) . $this->convert2Gzip(strlen($body));
    }

    /**
     * removes session identifiers from response body
     *
     * @param   string  $body
     * @return  string
     */
    protected function removeSessionIdentifiers($body)
    {
        return str_replace(array('$SESSION_ID', '$SESSION_NAME', '$SID'),
                           '',
                           $body
        );
    }

    /**
     * returns gzip-encoded value
     *
     * @param   int    $value
     * @return  string
     */
    protected function convert2Gzip($value)
    {
        $return = '';
        for ($i = 0; $i < 4; $i++) {
            $return .= chr($value % 256);
            $value   = floor($value / 256);
        }
        
        return $return;
    }

    /**
     * generates the cache key from given list of cache keys
     *
     * @param   string  $routeName  name of the route to be cached
     * @return  string
     */
    protected function generateCacheKey($routeName)
    {
        return parent::generateCacheKey($routeName) . '.gz';
    }
}
?>