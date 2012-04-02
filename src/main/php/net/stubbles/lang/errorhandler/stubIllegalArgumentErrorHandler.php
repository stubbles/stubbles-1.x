<?php
/**
 * Error handler for illegal arguments.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @version     $Id: stubIllegalArgumentErrorHandler.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubErrorHandler',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Error handler for illegal arguments.
 * 
 * This error handler is responsible for errors of type E_RECOVERABLE_ERROR which denote that
 * a type hint has been infringed with an argument of another type. If such an error is detected
 * an stubIllegalArgumentException will be thrown.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @see         http://php.net/set_error_handler
 */
class stubIllegalArgumentErrorHandler extends stubBaseObject implements stubErrorHandler
{
    /**
     * checks whether this error handler is responsible for the given error
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
        if (E_RECOVERABLE_ERROR != $level) {
            return false;
        }
        
        return (bool) preg_match('/Argument [0-9]+ passed to [a-zA-Z0-9_]+::[a-zA-Z0-9_]+\(\) must be an instance of [a-zA-Z0-9_]+, [a-zA-Z0-9_]+ given/', $message);
    }

    /**
     * checks whether this error is supressable
     * 
     * This method is called in case the level is 0. A type hint infringement
     * is never supressable.
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
        return false;
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
     * @throws  stubIllegalArgumentException
     */
    public function handle($level, $message, $file = null, $line = null, array $context = array())
    {
        throw new stubIllegalArgumentException($message . ' @ ' . $file . ' on line ' . $line);
    }
}
?>