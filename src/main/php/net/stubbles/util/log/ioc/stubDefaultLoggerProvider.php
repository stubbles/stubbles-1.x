<?php
/**
 * Injection provider for logger instances with a file appender.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc
 * @version     $Id: stubDefaultLoggerProvider.php 2106 2009-02-16 23:33:38Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::util::log::stubLogger',
                      'net::stubbles::util::log::appender::stubFileLogAppender'
);
/**
 * Injection provider for logger instances with a file appender.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc
 */
class stubDefaultLoggerProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * basic logger instance
     *
     * @var  stubLogger
     */
    protected $logger;
    /**
     * path where logfiles should be stored
     *
     * @var  string
     */
    protected $logPath;

    /**
     * constructor
     *
     * @param  stubLogger  $logger   basic logger instance
     * @param  string      $logPath  path where logfiles should be stored
     * @Inject
     * @Named{logger}('util.log.baseLogger')
     * @Named{logPath}('net.stubbles.log.path')
     */
    public function __construct(stubLogger $logger, $logPath)
    {
        $this->logger  = $logger;
        $this->logPath = $logPath;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        if ($this->logger->hasLogAppenders() === false) {
            $this->logger->addLogAppender(new stubFileLogAppender($this->logPath));
        }
        
        return $this->logger;
    }
}
?>