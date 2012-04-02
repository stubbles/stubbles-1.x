<?php
/**
 * Preinterceptor that is able to display the last created XML result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml
 * @version     $Id: stubShowLastXmlInterceptor.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubPreInterceptor');
/**
 * Preinterceptor that is able to display the last created XML result document.
 *
 * This interceptor cancels the request in case the request param
 * showLastRequestXML is set and the session is not new.
 *
 * @package     stubbles
 * @subpackage  webapp_xml
 */
class stubShowLastXmlInterceptor extends stubBaseObject implements stubPreInterceptor
{
    /**
     * does the preprocessing stuff
     *
     * @param  stubRequest   $request   access to request data
     * @param  stubSession   $session   access to session data
     * @param  stubResponse  $response  access to response data
     */
    public function preProcess(stubRequest $request, stubSession $session, stubResponse $response)
    {
        if ($request->hasParam('showLastRequestXML') === true && $session->isNew() === false) {
            $response->addHeader('Content-type', 'text/xml');
            $response->write($session->getValue('net.stubbles.webapp.lastRequestResponseData'));
            $request->cancel();
        }
    }
}
?>