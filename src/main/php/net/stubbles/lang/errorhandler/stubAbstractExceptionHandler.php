<?php
/**
 * Abstract base implementation for exception handlers, containing logging of exceptions.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @version     $Id: stubAbstractExceptionHandler.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubBaseResponse',
                      'net::stubbles::lang::errorhandler::stubExceptionHandler',
                      'net::stubbles::util::log::stubLogger'
);
/**
 * Abstract base implementation for exception handlers, containing logging of exceptions.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler
 * @see         http://php.net/set_exception_handler
 */
abstract class stubAbstractExceptionHandler extends stubBaseObject implements stubExceptionHandler
{
    /**
     * path to project
     *
     * @var  string
     */
    protected $projectPath;
    /**
     * switch whether logging is enabled or not
     *
     * @var  bool
     */
    protected $loggingEnabled = true;
    /**
     * target of the log data
     *
     * @var  string
     */
    protected $logTarget      = 'exceptions';
    /**
     * directory to log errors into
     *
     * @var  string
     */
    protected $logDir;
    /**
     * mode for new directories
     *
     * @var  int
     */
    protected $mode           = 0700;

    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     */
    public function __construct($projectPath)
    {
        $this->projectPath = $projectPath;
        $this->logDir      = $projectPath . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '{Y}' . DIRECTORY_SEPARATOR . '{M}';
    }

    /**
     * set whether logging is enabled or not
     *
     * @param  bool  $loggingEnabled
     * @deprecated  will be removed with 1.8.0 or 2.0.0
     */
    public function setLogging($loggingEnabled)
    {
        $this->loggingEnabled = $loggingEnabled;
    }

    /**
     * enables exception logging
     *
     * @return  stubAbstractExceptionHandler
     */
    public function enableLogging()
    {
        $this->loggingEnabled = true;
        return $this;
    }

    /**
     * disables exception logging
     *
     * @return  stubAbstractExceptionHandler
     */
    public function disableLogging()
    {
        $this->loggingEnabled = false;
        return $this;
    }

    /**
     * sets the target of the log data
     *
     * @param   string                        $logTarget
     * @return  stubAbstractExceptionHandler
     */
    public function setLogTarget($logTarget)
    {
        $this->logTarget = $logTarget;
        return $this;
    }

    /**
     * sets the mode for new log directories
     *
     * @param   int                           $mode
     * @return  stubAbstractExceptionHandler
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * handles the exception
     *
     * @param  Exception  $exception  the uncatched exception
     */
    public function handleException(Exception $exception)
    {
        $response = new stubBaseResponse();
        $response->setStatusCode(null);
        $this->fillResponse($response, $exception);
        if (true === $this->loggingEnabled) {
            $this->log($exception);
        }
        
        // send the response because the request will end right after this
        // method has been finished
        $response->send();
    }

    /**
     * fills response with useful data for display
     *
     * @param  stubResponse  $response   response to be send
     * @param  Exception     $exception  the uncatched exception
     */
    protected abstract function fillResponse(stubResponse $response, Exception $exception);

    /**
     * logs the exception into a logfile
     *
     * @param  Exception  $exception  the uncatched exception
     */
    protected function log(Exception $exception)
    {
        $logData  = date('Y-m-d H:i:s');
        $logData .= '|' . ((($exception instanceof stubThrowable) === true) ? ($exception->getClassName()) : (get_class($exception)));
        $logData .= '|' . $exception->getMessage();
        $logData .= '|' . $exception->getFile();
        $logData .= '|' . $exception->getLine();
        if ($exception instanceof stubChainedException && null !== $exception->getCause()) {
            $cause = $exception->getCause();
            $logData .= '|' . (($cause instanceof stubThrowable) ? ($cause->getClassName()) : (get_class($cause)));
            $logData .= '|' . $cause->getMessage();
            $logData .= '|' . $cause->getFile();
            $logData .= '|' . $cause->getLine();
        } else {
            $logData .= '||||';
        }
        
        $logDir = str_replace('{Y}', date('Y'), str_replace('{M}', date('m'), $this->logDir));
        if (file_exists($logDir) === false) {
            mkdir($logDir, $this->mode, true);
        }
        
        error_log($logData . "\n", 3, $logDir . DIRECTORY_SEPARATOR . $this->logTarget . '-' . date('Y-m-d') . '.log');
    }
}