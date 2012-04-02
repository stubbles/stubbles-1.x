<?php
/**
 * Abstract base implementation of a log entry factory.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory
 * @version     $Id: stubAbstractLogEntryFactory.php 2442 2010-01-11 16:33:44Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::entryfactory::stubLogEntryFactory');
/**
 * Abstract base implementation of a log entry factory.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory
 * @since       1.1.0
 */
abstract class stubAbstractLogEntryFactory extends stubBaseObject implements stubLogEntryFactory
{
    /**
     * recreates given log entry
     *
     * @param   stubLogEntry  $logEntry
     * @param   stubLogger    $logger
     * @return  stubLogEntry
     */
    public function recreate(stubLogEntry $logEntry, stubLogger $logger)
    {
        return $logEntry;
    }
}
?>