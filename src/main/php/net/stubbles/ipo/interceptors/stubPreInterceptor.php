<?php
/**
 * interface for preinterceptors
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @version     $Id: stubPreInterceptor.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession'
);
/**
 * interface for preinterceptors
 * 
 * Preinterceptors are called after all initializations have been done and
 * before processing of data starts.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 */
interface stubPreInterceptor extends stubObject
{
    /**
     * does the preprocessing stuff
     *
     * @param  stubRequest   $request   access to request data
     * @param  stubSession   $session   access to session data
     * @param  stubResponse  $response  access to response data
     */
    public function preProcess(stubRequest $request, stubSession $session, stubResponse $response);
}
?>