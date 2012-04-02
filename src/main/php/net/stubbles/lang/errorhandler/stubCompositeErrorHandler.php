<?php
/**
 * Container for a collection of PHP error handlers.
 * 
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @version     $Id: stubCompositeErrorHandler.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubErrorHandler');
/**
 * Container for a collection of PHP error handlers.
 * 
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @see         http://php.net/set_error_handler
 */
class stubCompositeErrorHandler extends stubBaseObject implements stubErrorHandler
{
    /**
     * list of registered error handlers
     *
     * @var  array<stubErrorHandler>
     */
    protected $errorHandlers = array();

    /**
     * adds an error handler to the collection
     *
     * @param  stubErrorHandler  $errorHandler
     */
    public function addErrorHandler(stubErrorHandler $errorHandler)
    {
        $this->errorHandlers[] = $errorHandler;
    }

    /**
     * returns the list of error handlers
     *
     * @return  array<stubErrorHandler>
     */
    public function getErrorHandlers()
    {
        return $this->errorHandlers;
    }

    /**
     * checks whether this error handler is responsible for the given error
     * 
     * This method is called in case the level is 0. It decides whether the
     * error has to be handled or if it can be omitted.
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error handler is responsible, else false
     */
    public function isResponsible($level, $message, $file = null, $line = null, array $context = array())
    {
        foreach ($this->errorHandlers as $errorHandler) {
            if ($errorHandler->isResponsible($level, $message, $file, $line, $context) == true) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * checks whether this error is supressable
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error is supressable, else false
     */
    public function isSupressable($level, $message, $file = null, $line = null, array $context = array())
    {
        foreach ($this->errorHandlers as $errorHandler) {
            if ($errorHandler->isSupressable($level, $message, $file, $line, $context) == false) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * handles the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error message should populate $php_errormsg, else false
     * @throws  stubException  error handlers are allowed to throw every exception they want to
     */
    public function handle($level, $message, $file = null, $line = null, array $context = array())
    {
        $errorReporting = error_reporting();
        foreach ($this->errorHandlers as $errorHandler) {
            if ($errorHandler->isResponsible($level, $message, $file, $line, $context) == true) {
                // if function/method was called with prepended @ and error is supressable
                if (0 == $errorReporting && $errorHandler->isSupressable($level, $message, $file, $line, $context) == true) {
                    return true;
                }
                
                return $errorHandler->handle($level, $message, $file, $line, $context);
            }
        }
        
        return true;
    }
}
?>