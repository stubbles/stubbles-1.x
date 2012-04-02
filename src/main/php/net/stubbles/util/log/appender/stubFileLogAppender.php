<?php
/**
 * A log appenders that writes log entries to files.
 *
 * @package     stubbles
 * @subpackage  util_log_appender
 * @version     $Id: stubFileLogAppender.php 3220 2011-11-14 15:33:46Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::appender::stubLogAppender');
/**
 * A log appenders that writes log entries to files.
 * 
 * This log appender writes the log entries into a logfile using the error_log()
 * function of PHP. The logfile name will be [target]-[Y-m-d].log where target
 * is the return value of stubLogEntry::getTarget().
 *
 * @package     stubbles
 * @subpackage  util_log_appender
 * @uses        http://php.net/error_log
 */
class stubFileLogAppender extends stubBaseObject implements stubLogAppender
{
    /**
     * the directory to write the logfiles into
     *
     * @var  string
     */
    protected $logDir = '';
    /**
     * mode for new directories
     *
     * @var  int
     */
    protected $mode   = 0700;

    /**
     * constructor
     *
     * @param  string  $logDir  optional  directory to write the logfiles into
     */
    public function __construct($logDir)
    {
        $this->logDir = $logDir;
    }

    /**
     * returns the directory to write the logfiles into
     *
     * @return  string
     * @deprecated  will be removed with 1.8.0 or 2.0.0
     */
    public function getLogDir()
    {
        return $this->logDir;
    }

    /**
     * sets the mode for new log directories
     *
     * @param   int                  $mode
     * @return  stubFileLogAppender
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * returns the mode for new log directories
     *
     * @return  int
     * @deprecated  will be removed with 1.8.0 or 2.0.0
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * append the log entry to the log file
     * 
     * The basename of the logfile will be [target]-[Y-m-d].log where target
     * is the return value of $logEntry->getTarget().
     *
     * @param   stubLogEntry     $logEntry
     * @return  stubLogAppender
     */
    public function append(stubLogEntry $logEntry)
    {
        if (file_exists($this->logDir) === false) {
            mkdir($this->logDir, $this->mode, true);
        }
        
        error_log($logEntry->get() . "\n",
                  3,
                  $this->logDir . DIRECTORY_SEPARATOR . $logEntry->getTarget() . '-' . date('Y-m-d') . '.log'
        );
        return $this;
    }

    /**
     * finalize the log target
     */
    public function finalize()
    {
        // nothing to do, therefore intentionelly left blank
    }
}
?>