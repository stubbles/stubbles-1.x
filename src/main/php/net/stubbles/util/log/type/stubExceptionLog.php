<?php
/**
 * Log exceptions.
 *
 * @package     stubbles
 * @subpackage  util_log_types
 * @version     $Id: stubExceptionLog.php 2342 2009-10-06 13:02:37Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::stubLogger');
/**
 * Log exceptions.
 *
 * @package     stubbles
 * @subpackage  util_log_types
 */
class stubExceptionLog extends stubBaseObject
{
    /**
     * logger instance
     *
     * @var  stubLogger
     */
    protected $logger;
    /**
     * target of the log data
     *
     * @var  string
     */
    protected $logTarget = 'exceptions';

    /**
     * constructor
     *
     * @param  stubLogger  $logger
     */
    public function __construct(stubLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * static constructor
     *
     * @param   stubLogger        $logger
     * @return  stubExceptionLog
     */
    public static function create(stubLogger $logger)
    {
        return new self($logger);
    }

    /**
     * sets the target of the log data
     *
     * @param   string            $logTarget
     * @return  stubExceptionLog
     */
    public function setLogTarget($logTarget)
    {
        $this->logTarget = $logTarget;
        return $this;
    }

    /**
     * logs an exception
     *
     * @param  Exception  $exception
     */
    public function log(Exception $exception)
    {
        $logEntry = $this->logger->createLogEntry($this->logTarget);
        $logEntry->addData(($exception instanceof stubThrowable) ? ($exception->getClassName()) : (get_class($exception)))
                 ->addData($exception->getMessage())
                 ->addData($exception->getFile())
                 ->addData($exception->getLine());
        if ($exception instanceof stubChainedException && null !== $exception->getCause()) {
            $cause = $exception->getCause();
            $logEntry->addData(($cause instanceof stubThrowable) ? ($cause->getClassName()) : (get_class($cause)))
                     ->addData($cause->getMessage())
                     ->addData($cause->getFile())
                     ->addData($cause->getLine());
        } else {
            $logEntry->addData('')
                     ->addData('')
                     ->addData('')
                     ->addData('');
        }
        
        $logEntry->log();
    }
}
?>