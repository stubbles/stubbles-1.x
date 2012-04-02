<?php
/**
 * Interface for exception handlers.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @version     $Id: stubExceptionHandler.php 2082 2009-02-10 12:48:36Z mikey $
 */
/**
 * Interface for exception handlers.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @see         http://php.net/set_exception_handler
 */
interface stubExceptionHandler extends stubObject
{
    /**
     * handles the exception
     *
     * @param  Exception  $exception  the uncatched exception
     */
    public function handleException(Exception $exception);
}
?>