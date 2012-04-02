<?php
/**
 * Factory which creates empty log entry containers.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory
 * @version     $Id: stubEmptyLogEntryFactory.php 2438 2010-01-07 16:28:49Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::entryfactory::stubAbstractLogEntryFactory');
/**
 * Factory which creates empty log entry containers.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory
 */
class stubEmptyLogEntryFactory extends stubAbstractLogEntryFactory
{
    /**
     * creates a log entry container
     *
     * @param   string        $target  target where the log data should go to
     * @param   stubLogger    $logger  logger instance to create log entry container for
     * @return  stubLogEntry
     */
    public function create($target, stubLogger $logger)
    {
        return new stubLogEntry($target, $logger);
    }
}
?>