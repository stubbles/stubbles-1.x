<?php
/**
 * Interface for factories which create log entry containers.
 *
 * @package     stubbles
 * @subpackage  util_log
 * @version     $Id: stubLogEntryFactory.php 2438 2010-01-07 16:28:49Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::stubLogEntry',
                      'net::stubbles::util::log::stubLogger'
);
/**
 * Interface for factories which create log entry containers.
 *
 * @package     stubbles
 * @subpackage  util_log
 */
interface stubLogEntryFactory extends stubObject
{
    /**
     * creates a log entry container
     *
     * @param   string        $target  target where the log data should go to
     * @param   stubLogger    $logger  logger instance to create log entry container for
     * @return  stubLogEntry
     */
    public function create($target, stubLogger $logger);

    /**
     * recreates given log entry
     *
     * @param   stubLogEntry  $logEntry
     * @param   stubLogger    $logger
     * @return  stubLogEntry
     * @since   1.1.0
     */
    public function recreate(stubLogEntry $logEntry, stubLogger $logger);
}
?>