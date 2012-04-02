<?php
/**
 * Handlings for different runtime modes of Stubbles.
 *
 * @package     stubbles
 * @subpackage  lang
 * @version     $Id: stubDefaultMode.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubMode',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Handlings for different runtime modes of Stubbles.
 *
 * The mode instance contains information about which exception handler and
 * which error handler should be used, else well as whether caching is enabled
 * or not. Currently, there are four different default modes available:
 * stubDefaultMode::prod()
 *      - uses exception handler net::stubbles::lang::errorhandler::stubProdModeExceptionHandler
 *      - uses default error handler net::stubbles::lang::errorhandler::stubDefaultErrorHandler
 *      - caching enabled
 * stubDefaultMode::test()
 *      - uses exception handler net::stubbles::lang::errorhandler::stubDisplayExceptionHandler
 *      - uses default error handler net::stubbles::lang::errorhandler::stubDefaultErrorHandler
 *      - caching enabled
 * stubDefaultMode::stage()
 *      - uses exception handler net::stubbles::lang::errorhandler::stubDisplayExceptionHandler
 *      - no error handler
 *      - caching disabled
 * stubDefaultMode::dev()
 *      - uses exception handler net::stubbles::lang::errorhandler::stubDisplayExceptionHandler
 *      - no error handler
 *      - caching disabled
 * While stage and dev mode currently are not different this may change in
 * future in case new mode depending switches become neccessary.
 *
 * To change the exception and/or error handler to be used, set the new ones
 * via setExceptionHandler()/setErrorHandler().
 * Please be aware that you still need to register the exception/error handler,
 * this is not done automatically, regardless whether you set your own ones or
 * not. Use registerExceptionHandler() and registerErrorHandler() to do so.
 *
 * @package     stubbles
 * @subpackage  lang
 */
class stubDefaultMode extends stubBaseObject implements stubMode
{
    /**
     * name of mode
     *
     * @var  string
     */
    protected $modeName;
    /**
     * exception handler to be used in the mode
     *
     * @var  array<string,string>
     */
    protected $exceptionHandler = null;
    /**
     * error handler to be used in the mode
     *
     * @var  array<string,string>
     */
    protected $errorHandler     = null;
    /**
     * switch whether cache should be enabled in mode or not
     *
     * @var  bool
     */
    protected $cacheEnabled     = true;

    /**
     * constructor
     *
     * Use this to create your own mode. However you might want to use one of
     * the four default modes delivered by this class, see below for the static
     * constructor methods prod(), test(), stage() and dev().
     *
     * @param  string                $modeName
     * @param  array<string,string>  $exceptionHandler
     * @param  array<string,string>  $errorHandler
     * @param  bool                  $cacheEnabled
     */
    public function __construct($modeName, array $exceptionHandler, array $errorHandler, $cacheEnabled)
    {
        $this->modeName         = $modeName;
        $this->exceptionHandler = ((count($exceptionHandler) > 0) ? ($exceptionHandler) : (null));
        $this->errorHandler     = ((count($errorHandler) > 0) ? ($errorHandler) : (null));
        $this->cacheEnabled     = $cacheEnabled;
    }

    /**
     * creates default production mode
     *
     * - exceptions will be logged, error 500 will be shown instead of exception
     * - default error handling for PHP errors
     * - caching enabled
     *
     * @return  stubMode
     */
    public static function prod()
    {
        return new self('PROD',
                        array('class'  => 'net::stubbles::lang::errorhandler::stubProdModeExceptionHandler',
                              'method' => 'handleException',
                              'type'   => stubMode::HANDLER_INSTANCE
                        ),
                        array('class'  => 'net::stubbles::lang::errorhandler::stubDefaultErrorHandler',
                              'method' => 'handle',
                              'type'   => stubMode::HANDLER_INSTANCE
                        ),
                        true
               );
    }

    /**
     * creates default test mode
     *
     * - exceptions will be displayed
     * - default error handling for PHP errors
     * - caching enabled
     *
     * @return  stubMode
     */
    public static function test()
    {
        return new self('TEST',
                        array('class'  => 'net::stubbles::lang::errorhandler::stubDisplayExceptionHandler',
                              'method' => 'handleException',
                              'type'   => stubMode::HANDLER_INSTANCE
                        ),
                        array('class'  => 'net::stubbles::lang::errorhandler::stubDefaultErrorHandler',
                              'method' => 'handle',
                              'type'   => stubMode::HANDLER_INSTANCE
                        ),
                        true
               );
    }

    /**
     * creates default stage mode
     *
     * - exceptions will be displayed
     * - no error handling for PHP errors
     * - caching disabled
     *
     * @return  stubMode
     */
    public static function stage()
    {
        return new self('STAGE',
                        array('class'  => 'net::stubbles::lang::errorhandler::stubDisplayExceptionHandler',
                              'method' => 'handleException',
                              'type'   => stubMode::HANDLER_INSTANCE
                        ),
                        array(),
                        false
               );
    }

    /**
     * creates default dev mode
     *
     * - exceptions will be displayed
     * - no error handling for PHP errors
     * - caching disabled
     *
     * @return  stubMode
     */
    public static function dev()
    {
        return new self('DEV',
                        array('class'  => 'net::stubbles::lang::errorhandler::stubDisplayExceptionHandler',
                              'method' => 'handleException',
                              'type'   => stubMode::HANDLER_INSTANCE
                        ),
                        array(),
                        false
               );
    }

    /**
     * returns the name of the mode
     *
     * @return  string
     */
    public function name()
    {
        return $this->modeName;
    }

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
    public function setExceptionHandler($class, $methodName, $type = stubMode::HANDLER_INSTANCE)
    {
        $this->exceptionHandler = array('class'  => $class,
                                        'method' => $methodName,
                                        'type'   => $type
                                  );
        return $this;
    }

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
    public function registerExceptionHandler($projectPath)
    {
        if (null === $this->exceptionHandler) {
            return false;
        }
        
        $callback = $this->getCallback($this->exceptionHandler, $projectPath);
        set_exception_handler($callback);
        if (is_object($callback[0]) === true) {
            return $callback[0];
        }
        
        return true;
    }

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
    public function setErrorHandler($class, $methodName, $type = stubMode::HANDLER_INSTANCE)
    {
        $this->errorHandler = array('class'  => $class,
                                    'method' => $methodName,
                                    'type'   => $type
                              );
        return $this;
    }

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
    public function registerErrorHandler($projectPath)
    {
        if (null === $this->errorHandler) {
            return false;
        }
        
        $callback = $this->getCallback($this->errorHandler, $projectPath);
        set_error_handler($callback);
        if (is_object($callback[0]) === true) {
            return $callback[0];
        }
        
        return true;
    }

    /**
     * checks whether cache is enabled or not
     *
     * @return  bool
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * helper method to create the callback from the handler data
     *
     * @param   array     &$handler     handler data
     * @param   string    $projectPath  path to project
     * @return  callback
     * @throws  stubIllegalArgumentException
     */
    protected function getCallback(array &$handler, $projectPath)
    {
        if (is_string($handler['class']) === true && class_exists($handler['class'], false) === false) {
            stubClassLoader::load($handler['class']);
        }
        
        if (stubMode::HANDLER_INSTANCE === $handler['type']) {
            if (is_string($handler['class']) === true) {
                $class    = stubClassLoader::getNonQualifiedClassName($handler['class']);
                $instance = new $class($projectPath);
            } else {
                $instance = $handler['class'];
            }
            
             return array($instance, $handler['method']);
        }
        
        if (is_string($handler['class']) === false) {
            throw new stubIllegalArgumentException('Callback type should be stubMode::HANDLER_STATIC, but given handler class is an instance.');
        }
        
        return array(stubClassLoader::getNonQualifiedClassName($handler['class']), $handler['method']);
    }
}
?>