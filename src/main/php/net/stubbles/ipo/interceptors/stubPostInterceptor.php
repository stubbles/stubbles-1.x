<?php
/**
 * interface for postinterceptors
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @version     $Id: stubPostInterceptor.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession'
);
/**
 * interface for postinterceptors
 * 
 * Postinterceptors are called after all data processing is done. They can change
 * the response or add additional data to the response.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 */
interface stubPostInterceptor extends stubObject
{
    /**
     * does the postprocessing stuff
     *
     * @param  stubRequest   $request   access to request data
     * @param  stubSession   $session   access to session data
     * @param  stubResponse  $response  access to response data
     */
    public function postProcess(stubRequest $request, stubSession $session, stubResponse $response);
}
?>