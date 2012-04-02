<?php
/**
 * Post Interceptor which adds an ETag header to the
 * response to avoid dispensable traffic and save download
 * time for the user.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @version     $Id: stubETagPostInterceptor.php 3212 2011-11-10 21:20:11Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubPostInterceptor');
/**
 * Post Interceptor which adds an ETag header to the
 * response to avoid dispensable traffic and save download
 * time for the user.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @link        http://www.w3.org/Protocols/rfc2616/rfc2616.html  RFC 2616  Hypertext Transfer Protocol -- HTTP/1.1 (mainly section 14)
 * @link        http://betterexplained.com/articles/how-to-optimize-your-site-with-http-caching/
 */
class stubETagPostInterceptor extends stubBaseObject implements stubPostInterceptor
{
    /**
     * does the postprocessing stuff
     *
     * @param  stubRequest   $request   access to request data
     * @param  stubSession   $session   access to session data
     * @param  stubResponse  $response  access to response data
     */
    public function postProcess(stubRequest $request, stubSession $session, stubResponse $response)
    {
        if (in_array($response->getStatusCode(), array(200, 203, 206, 300, 301, 410)) === false) {
            return;
        }
        
        // the double quotes are part of an ETag
        $etag = '"' . md5(serialize($response->getBody())) . '"';
        $response->addHeader('ETag', $etag);

        // these headers interfere with the ETag header,
        // therefore they must be overidden
        $response->addHeader('Cache-Control', 'private');  // HTTP 1.1
        $response->addHeader('Pragma', '');                // HTTP 1.0 (for backward compatibility)

        if ($request->validateHeader('HTTP_IF_NONE_MATCH')->isEqualTo($etag) === true) {
            $response->setStatusCode(304)
                     ->clearBody();
        }
    }
}
?>