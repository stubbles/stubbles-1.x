<?php
/**
 * Handlings for different runtime modes of Stubbles.
 *
 * @package     stubbles
 * @subpackage  lang
 * @version     $Id: stubMode.php 3226 2011-11-23 16:14:05Z mikey $
 */
/**
 * Handlings for different runtime modes of Stubbles.
 *
 * The mode instance contains information about which exception handler and
 * which error handler should be used, else well as whether caching is enabled
 * or not.
 *
 * @package     stubbles
 * @subpackage  lang
 */
interface stubMode extends stubObject
{
    /**
     * handler method must be called statically
     */
    const HANDLER_STATIC   = 'static';
    /**
     * handler has to be an instance
     */
    const HANDLER_INSTANCE = 'instance';

    /**
     * returns the name of the mode
     *
     * @return  string
     */
    public function name();

    /**
     * sets the exception handler to given class and method name
     *
     * To register the new exception handler call registerExceptionHandler().
     *
     * @param   string|object  $class        name or instance of exception handler class
     * @param   string         $methodName   name of exception handler method
     * @param   string         $type         optional  whether method has to be called statically or via an instance
     * @return  stubMode
     */
    public function setExceptionHandler($class, $methodName, $type = self::HANDLER_INSTANCE);

    /**
     * registers exception handler for current mode
     *
     * Return value depends on registration: if no exception handler set return
     * value will be false, if registered handler was an instance the handler
     * instance will be returned, and true in any other case.
     *
     * @param   string       $projectPath  path to project
     * @return  bool|object
     */
    public function registerExceptionHandler($projectPath);

    /**
     * sets the error handler to given class and method name
     *
     * To register the new error handler call registerErrorHandler().
     *
     * @param   string|object  $class        name or instance of error handler class
     * @param   string         $methodName   name of error handler method
     * @param   string         $type         optional  whether method has to be called statically or via an instance
     * @return  stubMode
     */
    public function setErrorHandler($class, $methodName, $type = self::HANDLER_INSTANCE);

    /**
     * registers error handler for current mode
     *
     * Return value depends on registration: if no error handler set return value
     * will be false, if registered handler was an instance the handler instance
     * will be returned, and true in any other case.
     *
     * @param   string       $projectPath  path to project
     * @return  bool|object
     */
    public function registerErrorHandler($projectPath);

    /**
     * checks whether cache is enabled or not
     *
     * @return  bool
     */
    public function isCacheEnabled();
}
?>