<?php
/**
 * Class for logging.
 *
 * @package     stubbles
 * @subpackage  util_log
 * @version     $Id: stubLogger.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::stubLogEntry',
                      'net::stubbles::util::log::appender::stubLogAppender',
                      'net::stubbles::util::log::entryfactory::stubLogEntryFactory'
);
/**
 * Class for logging.
 * 
 * The logger is the interface to log data into differant targets. The logger
 * itself does not know where to write the log data - it just uses log appenders
 * which in turn do the real work. A logger is a collection of such appenders.
 *
 * @package     stubbles
 * @subpackage  util_log
 */
class stubLogger extends stubBaseObject
{
    /**
     * log level: no logging
     */
    const LEVEL_NONE             = 0;
    /**
     * log level: debug only
     */
    const LEVEL_DEBUG            = 1;
    /**
     * log level: info data only
     */
    const LEVEL_INFO             = 2;
    /**
     * log level: warnings only
     */
    const LEVEL_WARN             = 4;
    /**
     * log level: errors only
     */
    const LEVEL_ERROR            = 8;
    /**
     * log level: all
     */
    const LEVEL_ALL              = 15;
    /**
     * factory to be used to create log data containers
     *
     * @var  stubLogEntryFactory
     */
    protected $logEntryFactory;
    /**
     * list of log appenders to log data to
     *
     * @var  array<stubLogAppender>
     */
    protected $logAppender       = array();
    /**
     * list of delayed log entries
     *
     * @var  array<stubLogEntry>
     */
    protected $delayedLogEntries = array();

    /**
     * constructor
     *
     * @param  stubLogEntryFactory  $logEntryFactory  factory to be used to create log data containers
     */
    public function __construct(stubLogEntryFactory $logEntryFactory)
    {
        $this->logEntryFactory = $logEntryFactory;
    }

    /**
     * destructor
     * 
     * Calls all log appenders that they should finalize their work.
     */
    public final function __destruct()
    {
        $this->processDelayedLogEntries();
        foreach ($this->logAppender as $logAppender) {
            $logAppender->finalize();
        }
    }

    /**
     * adds a log appender to the logger
     * 
     * A log appender is responsible for writing the log data.
     *
     * @param   stubLogAppender  $logAppender
     * @return  stubLogAppender  the freshly added log appender instance
     */
    public function addLogAppender(stubLogAppender $logAppender)
    {
        $this->logAppender[] = $logAppender;
        return $logAppender;
    }

    /**
     * checks whether logger has any log appenders
     *
     * @return  bool
     */
    public function hasLogAppenders()
    {
        return (count($this->logAppender) > 0);
    }

    /**
     * returns a list of log appenders appended to the logger
     *
     * @return  array<stubLogAppender>
     */
    public function getLogAppenders()
    {
        return $this->logAppender;
    }

    /**
     * creates the log entry
     *
     * @param   string        $target
     * @return  stubLogEntry
     */
    public function createLogEntry($target)
    {
        return $this->logEntryFactory->create($target, $this);
    }

    /**
     * sends log data to all registered log appenders
     *
     * @param  stubLogEntry  $logEntry  contains data to log
     */
    public function log(stubLogEntry $logEntry)
    {
        foreach ($this->logAppender as $logAppender) {
            $logAppender->append($logEntry);
        }
    }

    /**
     * collects log entries but delays logging of them until destruction of the
     * logger or processDelayedLogEntries() gets called
     *
     * @param  stubLogEntry  $logEntry
     * @since  1.1.0
     */
    public function logDelayed(stubLogEntry $logEntry)
    {
        $this->delayedLogEntries[] = $logEntry;
    }

    /**
     * returns number of unprocessed delayed log entries
     *
     * @return  int
     * @since   1.1.0
     */
    public function hasUnprocessedDelayedLogEntries()
    {
        return (count($this->delayedLogEntries) > 0);
    }

    /**
     * processes all delayed log entries
     *
     * @return  int  amount of processed delayed entries
     * @since   1.1.0
     */
    public function processDelayedLogEntries()
    {
        if ($this->hasUnprocessedDelayedLogEntries() === false) {
            return 0;
        }

        foreach ($this->delayedLogEntries as $logEntry) {
            $this->log($this->logEntryFactory->recreate($logEntry, $this));
        }

        $amount = count($this->delayedLogEntries);
        $this->delayedLogEntries = array();
        return $amount;
    }
}
?>