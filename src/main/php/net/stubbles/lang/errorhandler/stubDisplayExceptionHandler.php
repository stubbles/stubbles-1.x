<?php
/**
 * Exception handler that displays the exception message nicely formated in the response.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @version     $Id: stubDisplayExceptionHandler.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubAbstractExceptionHandler');
/**
 * Exception handler that displays the exception message nicely formated in the response.
 *
 * You should not use this exception handler in production mode!
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 */
class stubDisplayExceptionHandler extends stubAbstractExceptionHandler
{
    /**
     * fills response with useful data for display
     *
     * @param  stubResponse  $response   response to be send
     * @param  Exception     $exception  the uncatched exception
     */
    protected function fillResponse(stubResponse $response, Exception $exception)
    {
        if ($exception instanceof stubThrowable) {
            $response->write((string) $exception);
        } else {
            $response->write($exception->getMessage());
        }
        
        $response->write("\nTrace:\n" . $exception->getTraceAsString());
    }
}