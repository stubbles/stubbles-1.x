<?php
/**
 * Container class for log data.
 *
 * @package     stubbles
 * @subpackage  util_log
 * @version     $Id: stubLogEntry.php 2438 2010-01-07 16:28:49Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::stubLogger');
/**
 * Container class for log data.
 *
 * @package     stubbles
 * @subpackage  util_log
 */
class stubLogEntry extends stubBaseObject
{
    /**
     * default seperator between log data fields
     */
    const DEFAULT_SEPERATOR = '|';
    /**
     * seperator between log data fields
     *
     * @var  string
     */
    protected $seperator    = self::DEFAULT_SEPERATOR;
    /**
     * logger to which the log data should be send
     *
     * @var  stubLogger
     */
    protected $logger;
    /**
     * target where the log data should go to
     *
     * @var  string
     */
    protected $target;
    /**
     * data to log
     *
     * @var  array<string>
     */
    protected $logData      = array();

    /**
     * constructor
     * 
     * How the target is interpreted depends on the log appender which
     * takes the log data. A file log appender might use this as the basename
     * of a file, while a database log appender might use this as the name
     * of the table to write the log data into. Therefore it is advisable to
     * only use ascii characters, numbers and underscores to be sure that the
     * log appender will not mess up the log data.
     *
     * @param  string      $target   target where the log data should go to
     * @param  stubLogger  $logger   logger to which the log data should be send
     */
    public function __construct($target, stubLogger $logger)
    {
        $this->target = $target;
        $this->logger = $logger;
    }

    /**
     * sets the seperator to be used
     *
     * @param   string        $seperator
     * @return  stubLogEntry
     */
    public function setSeperator($seperator)
    {
        $this->seperator = $seperator;
        return $this;
    }

    /**
     * logs the data using the given logger
     */
    public function log()
    {
        $this->logger->log($this);
    }

    /**
     * logs the data delayed using the given logger
     *
     * @since  1.1.0
     */
    public function logDelayed()
    {
        $this->logger->logDelayed($this);
    }

    /**
     * adds data to the log object
     * 
     * Each call to this method will add a new field. If the data contains line
     * breaks they will be replaced by <nl>. If the data contains the value of
     * the seperator or windows line feeds they will be removed.
     *
     * If the data starts with a double quote but does not end with a double
     * quote a closing double quote will be appended.
     *
     * If the data consists only of a single double quote it will be removed and
     * the added data string will thus be empty.
     *
     * @param   string        $data
     * @return  stubLogEntry
     */
    public function addData($data)
    {
        $this->logData[] = $this->escapeData($data);
        return $this;
    }

    /**
     * helper method to escape given data
     *
     * @param   string  $data
     * @return  string
     */
    protected function escapeData($data)
    {
        settype($data, 'string');
        $data = str_replace(chr(13), '', str_replace("\n", '<nl>', $data));
        if (strlen($data) > 1 && '"' === $data{0} && '"' !== $data{(strlen($data) - 1)}) {
            $data .= '"';
        } elseif (strlen($data) == 1 && '"' === $data) {
            $data = '';
        }

        return $data;
    }

    /**
     * replaces data within the log entry
     *
     * If the position to replace does not exist the replacement data will be
     * thrown away. The replacement data will be escaped the same way as when
     * added via addData().
     *
     * @param   int           $position         position to replace
     * @param   string        $replacementData  the data to replace the old data
     * @return  stubLogEntry
     * @since   1.1.0
     */
    public function replaceData($position, $replacementData)
    {
        if (isset($this->logData[$position]) === false) {
            return $this;
        }

        $this->logData[$position] = $this->escapeData($replacementData);
        return $this;
    }

    /**
     * returns whole log data
     *
     * @return  array<string>
     * @since   1.1.0
     */
    public function getData()
    {
        return array_map(array($this, 'escapeSeperator'), $this->logData);
    }

    /**
     * returns the whole log data on one line with fields seperated by the seperator
     *
     * @return  string
     */
    public function get()
    {
        return join($this->seperator, array_map(array($this, 'escapeSeperator'), $this->logData));
    }

    /**
     * escape string against seperator, i.e. remove it from data
     *
     * @param   string  $data
     * @return  string
     */
    protected function escapeSeperator($data)
    {
        return str_replace($this->seperator, '', $data);
    }

    /**
     * returns the target where the log data should go to
     * 
     * @return  string
     */
    public function getTarget()
    {
        return $this->target;
    }
}
?>