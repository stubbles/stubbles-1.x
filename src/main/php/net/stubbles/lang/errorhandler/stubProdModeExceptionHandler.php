<?php
/**
 * Exception handler for production mode: triggers a 500 Internal Server Error response.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @version     $Id: stubProdModeExceptionHandler.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubAbstractExceptionHandler');
/**
 * Exception handler for production mode: triggers a 500 Internal Server Error response.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 */
class stubProdModeExceptionHandler extends stubAbstractExceptionHandler
{
    /**
     * fills response with useful data for display
     *
     * @param  stubResponse  $response   response to be send
     * @param  Exception     $exception  the uncatched exception
     */
    protected function fillResponse(stubResponse $response, Exception $exception)
    {
        $response->setStatusCode(500);
        if (file_exists($this->projectPath . '/docroot/500.html') === true) {
            $response->write(file_get_contents($this->projectPath . '/docroot/500.html'));
        }
    }
}