<?php
/**
 * A log appenders that sends log data to a mail address.
 *
 * @package     stubbles
 * @subpackage  util_log_appender
 * @version     $Id: stubMailLogAppender.php 2060 2009-01-26 12:57:25Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubRuntimeException',
                      'net::stubbles::util::log::appender::stubLogAppender'
);
/**
 * A log appenders that sends log data to a mail address.
 *
 * This log appender writes the log data into a mail which will be send to
 * the configured mail address.
 *
 * @package     stubbles
 * @subpackage  util_log_appender
 */
class stubMailLogAppender extends stubBaseObject implements stubLogAppender
{
    /**
     * mail address to send the log data to
     *
     * @var  string
     */
    protected $mailAddress = null;
    /**
     * name to appear as sender
     *
     * @var  string
     */
    protected $senderName  = 'stubDebugger';
    /**
     * the collected log data
     *
     * @var  array<stubLogEntry>
     */
    protected $logEntries  = array();

    /**
     * constructor
     *
     * @param  string  $mailAddress  mail address to send the log data to
     * @param  string  $senderName   optional  name to appear as sender
     */
    public function __construct($mailAddress, $senderName = 'stubDebugger')
    {
        $this->mailAddress = $mailAddress;
        $this->senderName  = $senderName;
    }

    /**
     * returns mail address to send the log data to
     *
     * @return  string
     */
    public function getMailAddress()
    {
        return $this->mailAddress;
    }

    /**
     * returns name to appear as sender
     *
     * @return  string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * append the log data to the log target
     *
     * @param   stubLogEntry     $logEntry
     * @return  stubLogAppender
     */
    public function append(stubLogEntry $logEntry)
    {
        $this->logEntries[] = $logEntry;
        return $this;
    }

    /**
     * finalize the log target
     * 
     * @throws  stubRuntimeException
     */
    public function finalize()
    {
        if (count($this->logEntries) === 0) {
            return;
        }
        
        $body = '';
        foreach ($this->logEntries as $logEntry) {
            $body .= $logEntry->getTarget() . ': ' . $logEntry->get() . "\n\n";
        }
        
        $body .= sprintf("\nURL that caused this:\nhttp://%s%s?%s\n", $_SERVER['HTTP_HOST'], $_SERVER['PHP_SELF'], $_SERVER['QUERY_STRING']);
        if (isset($_SERVER['HTTP_REFERER']) === true) {
            $body .= sprintf("\nReferer:\n%s\n", $_SERVER['HTTP_REFERER']);
        }
        
        $this->sendMail('Debug message from ' . $_SERVER['HTTP_HOST'], $body);
    }

    /**
     * sends the mail
     *
     * @param  string  $subject  subject of the mail to send
     * @param  string  $body     body of the mail to send
     */
    // @codeCoverageIgnoreStart
    protected function sendMail($subject, $body)
    {
        mail($this->mailAddress, $subject, $body, 'FROM: ' . $this->senderName . ' <' . $this->mailAddress. '>');
    }
    // @codeCoverageIgnoreEnd
}
?>