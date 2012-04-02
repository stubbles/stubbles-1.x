<?php
/**
 * Interface for processor resolvers.
 * 
 * @package     stubbles
 * @subpackage  websites_processors
 * @version     $Id: stubProcessorResolver.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::websites::processors::stubProcessor'
);
/**
 * Interface for processor resolvers.
 * 
 * @package     stubbles
 * @subpackage  websites_processors
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
interface stubProcessorResolver extends stubObject
{
    /**
     * returns interceptor descriptor for given processor
     *
     * @param   stubRequest  $request  the current request
     * @return  string
     */
    public function getInterceptorDescriptor(stubRequest $request);

    /**
     * resolves the request and creates the appropriate processor
     *
     * @param   stubRequest    $request   the current request
     * @param   stubSession    $session   the current session
     * @param   stubResponse   $response  the current response
     * @return  stubProcessor
     */
    public function resolve(stubRequest $request, stubSession $session, stubResponse $response);
}
?>