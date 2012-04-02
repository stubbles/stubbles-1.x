<?php
/**
 * Default factory which create log entry containers.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory
 * @version     $Id: stubDefaultLogEntryFactory.php 2438 2010-01-07 16:28:49Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubSession',
                      'net::stubbles::util::log::entryfactory::stubAbstractLogEntryFactory'
);
/**
 * Default factory which create log entry containers.
 *
 * Log entry containers returned by this factory already have some prefilled log
 * data: the current timestamp in format Y-m-d H:i:s as first entry, and the
 * session id of the current user, if available.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory
 */
class stubDefaultLogEntryFactory extends stubAbstractLogEntryFactory
{
    /**
     * session instance
     *
     * @var  stubSession
     */
    protected $session;

    /**
     * sets the session
     *
     * @param  stubSession  $session  the session of the current user
     * @Inject(optional=true)
     */
    public function setSession(stubSession $session)
    {
        $this->session = $session;
    }

    /**
     * creates a log entry container
     *
     * @param   string        $target  target where the log data should go to
     * @param   stubLogger    $logger  logger instance to create log entry container for
     * @return  stubLogEntry
     */
    public function create($target, stubLogger $logger)
    {
        $logEntry = new stubLogEntry($target, $logger);
        $logEntry->addData(date('Y-m-d H:i:s'));
        if (null !== $this->session) {
            $logEntry->addData($this->session->getId());
        }
        
        return $logEntry;
    }
}
?>