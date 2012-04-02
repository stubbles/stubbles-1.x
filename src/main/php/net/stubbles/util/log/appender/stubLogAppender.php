<?php
/**
 * Interface for log appenders.
 *
  * @package     stubbles
 * @subpackage  util_log_appender
 * @version     $Id: stubLogAppender.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::stubLogEntry');
/**
 * Interface for log appenders.
 * 
 * A log appender takes log entries and writes them to the target. The target
 * can be a file, a database or anything else.
 *
 * @package     stubbles
 * @subpackage  util_log_appender
 */
interface stubLogAppender extends stubObject
{
    /**
     * append the log entry to the log target
     *
     * @param   stubLogEntry     $logEntry
     * @return  stubLogAppender
     */
    public function append(stubLogEntry $logEntry);

    /**
     * finalize the log target
     * 
     * This will be called in case a logger is destroyed and can be used
     * to close file or database handlers or to write the log data if
     * append() just collects the data.
     */
    public function finalize();
}
?>