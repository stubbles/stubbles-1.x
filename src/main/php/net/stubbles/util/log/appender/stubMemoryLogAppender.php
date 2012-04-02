<?php
/**
 * A log appenders that stores log entries in memory.
 *
 * @package     stubbles
 * @subpackage  util_log_appender
 * @version     $Id: stubMemoryLogAppender.php 2432 2009-12-28 18:14:55Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::appender::stubLogAppender');
/**
 * A log appenders that stores log entries in memory.
 *
 * @package     stubbles
 * @subpackage  util_log_appender
 */
class stubMemoryLogAppender extends stubBaseObject implements stubLogAppender
{
    /**
     * stores the logged entries and represents the storing medium (memory)
     *
     * @var  array<string,array<stubLogEntry>>
     */
    protected $logEntries = array();

    /**
     * counts log entries for a certain target
     *
     * @param   string  $target
     * @return  int
     * @since   1.1.0
     */
    public function countLogEntries($target)
    {
        if (isset($this->logEntries[$target]) === false) {
            return 0;
        }

        return count($this->logEntries[$target]);
    }

    /**
     * returns data of a certain log entry
     *
     * @param   string         $target
     * @param   int            $position
     * @return  array<string>
     * @since   1.1.0
     */
    public function getLogEntryData($target, $position)
    {
        if (isset($this->logEntries[$target]) === false) {
            return array();
        }

        if (isset($this->logEntries[$target][$position]) === false) {
            return array();
        }

        return $this->logEntries[$target][$position]->getData();
    }

    /**
     * returns list of log entries
     *
     * If a target is given only log entries of this target will be returned.
     *
     * @param   string                                                 $target  optional
     * @return  array<string,array<stubLogEntry>>|array<stubLogEntry>
     */
    public function getLogEntries($target = null)
    {
        if (null == $target) {
            return $this->logEntries;
        }

        if (isset($this->logEntries[$target]) === false) {
            return array();
        }

        return $this->logEntries[$target];
    }

    /**
     * stores log entry in memory
     *
     * @param   stubLogEntry     $logEntry
     * @return  stubLogAppender
     */
    public function append(stubLogEntry $logEntry)
    {
        $this->logEntries[$logEntry->getTarget()][] = $logEntry;
        return $this;
    }

    /**
     * finalize the log target
     */
    public function finalize()
    {
        $this->logEntries = array();
    }
}
?>