<?php
/**
 * Sends the given data to the FirePHP Firefox Extension (v0.1.2).
 *
 * @package     stubbles
 * @subpackage  service_debug
 * @version     $Id: stubFirebugLogger.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubBaseResponse');
/**
 * Sends the given data to the FirePHP Firefox Extension (v0.1.2).
 * The data can be displayed in the Firebug Console or in the
 * "Server" request tab (in Firebug: "Network > GET Request > Server tab").
 *
 * For more informtion see: http://www.firephp.org/
 *
 * This is not the original implementation of FirePHP Core but an
 * adapted implementation to our needs of FirePHP Core (v0.1.2).
 *
 * TRACE and TABLE aren't implemented yet.
 *
 * @package     stubbles
 * @subpackage  service_debug
 * @link        http://www.firephp.org/Wiki/Reference/Protocol
 */
class stubFirebugLogger
{
    /**
     * The FirePHP headers which have to be set.
     *
     * @var  array<string,string>
     */
    protected $headers;
    /**
     * The response which contains the headers.
     *
     * @var  stubResponse
     */
    protected $response;
    /**
     * Counter for unique HeaderIds.
     * 
     * Initialise with 2 because of header frame.
     *
     * @var  int
     */
    protected static $msgCount = 2;
    /**
     * Log level constant.
     */
    const LOG        = 'LOG';
    /**
     * Info level constant.
     */
    const INFO       = 'INFO';
    /**
     * Warn level constant.
     */
    const WARN       = 'WARN';
    /**
     * Error level constant.
     */
    const ERROR      = 'ERROR';
    /**
     * Dump constant.
     */
    const DUMP       = 'DUMP';
    /**
     * Exception constant.
     */
    const EXCEPTION  = 'EXCEPTION';
    /**
     * Amount of header fields without leading '2' (DUMP messages) or '3' (LOG messages).
     */
    const PAD_SLOTS = 11;

    /**
     * constructor
     *
     * @param  stubResponse  $response
     */
    public function __construct(stubResponse $response = null)
    {
        // header frame
        $this->headers['100000000001'] = '{';
        $this->headers['999999999999'] = '"__SKIP__":"__SKIP__"}';

        $this->response = ($response !== null) ? $response : new stubBaseResponse();
    }

    /**
     * Send headers finally.
     *
     * @param  array<string,string>  $headers
     */
    protected function sendHeaders($headers)
    {
        foreach ($headers as $numIndex => $jsonSnippet) {
            // skip normal headers
            if(strlen($numIndex) !== 12) {
                continue;
            }

            $this->response->addHeader('X-FirePHP-Data-'.$numIndex, $jsonSnippet);
        }
        $this->response->send();
    }

    /**
     * Processes logging messages.
     *
     * @param  mixed   $obj
     * @param  string  $level
     * @param  string  $label
     */
    protected function logWithLevel($obj, $level, $label = '')
    {
        $jsonString = $this->encodeToJson($obj);

        switch($level) {
            case self::DUMP:
                $numIndex = $this->indexCalculator('2');
                $this->headers[$numIndex] = '"'.$label.'":'.$jsonString.',';
                break;

            default:
                $numIndex = $this->indexCalculator('3');
                $level = (gettype($obj) === 'object' && $obj instanceof Exception) ? self::EXCEPTION : $level;
                $this->headers[$numIndex]  = '["'.$level.'",';
                $this->headers[$numIndex] .= ($label === '') ? $jsonString.'],' : '["'.$label.'",'.$jsonString.']],';
                break;
        }

        $this->setHeadersFor($level);
        $this->sendHeaders($this->headers);
    }

    /**
     * Encodes objects and adheres to the FirePHP protocol.
     *
     * @param   mixed   $obj
     * @return  string  json string
     */
    protected function encodeToJson($obj)
    {
        if(gettype($obj) === 'object') {
            if ($obj instanceof Exception) {
                $temp['Class']   = get_class($obj);
                $temp['Message'] = $obj->getMessage();
                $temp['File']    = str_replace('\\\\', '\\', $obj->getFile());
                $temp['Line']    = $obj->getLine();
                $temp['Type']    = 'throw';
                $temp['Trace']   = 'not implemented';
                $jsonString = json_encode($temp);
            } else {                                                 // get rid of leading '{'
                $jsonString = '{"__className":"'.get_class($obj).'",'.substr(json_encode($obj), 1);
            }
        } else {
            $jsonString = json_encode($obj);
        }
        return $jsonString;
    }

    /**
     * Sets the header frames for LOG or DUMP messages.
     *
     * @param  string  $dumpOrLog
     */
    protected function setHeadersFor($dumpOrLog)
    {
        switch($dumpOrLog) {
            case self::DUMP:
                $this->headers['200000000001'] = '"FirePHP.Dump":{';
                $this->headers['299999999999'] = '"__SKIP__":"__SKIP__"},';
                break;

            case self::LOG:
            case self::INFO:
            case self::WARN:
            case self::ERROR:
            case self::EXCEPTION:
            default:
                $this->headers['300000000001'] = '"FirePHP.Firebug.Console":[';
                $this->headers['399999999999'] = '["__SKIP__"]],';
                break;
        }
    }

    /**
     * Calculates the index number for a header message.
     * Provide the prefixed number (2 or 3).
     *
     * @param   string  $prefixedNumber
     * @return  string  e.g. '200000000003'
     */
    protected function indexCalculator($prefixedNumber)
    {
        $padding  = self::PAD_SLOTS - strlen(self::$msgCount);
        $numIndex = $prefixedNumber . str_pad('', $padding, '0') . self::$msgCount;
        self::$msgCount++;
        return $numIndex;
    }

    /**
     * Log a messagage with the 'log' level.
     *
     * @param  mixed   $obj
     * @param  string  $label
     */
    public function log($obj, $label = '')
    {
        $this->logWithLevel($obj, self::LOG, $label);
    }

    /**
     * Log a messagage with the 'info' level.
     *
     * @param  mixed   $obj
     * @param  string  $label
     */
    public function info($obj, $label = '')
    {
        $this->logWithLevel($obj, self::INFO, $label);
    }

    /**
     * Log a messagage with the 'warn' level.
     *
     * @param  mixed   $obj
     * @param  string  $label
     */
    public function warn($obj, $label = '')
    {
        $this->logWithLevel($obj, self::WARN, $label);
    }

    /**
     * Log a messagage with the 'error' level.
     *
     * @param  mixed   $obj
     * @param  string  $label
     */
    public function error($obj, $label = '')
    {
        $this->logWithLevel($obj, self::ERROR, $label);
    }

    /**
     * Log a messagage with the 'dump' level.
     *
     * @param  mixed   $obj
     * @param  string  $label
     */
    public function dump($obj, $label = 'unknown')
    {
        $this->logWithLevel($obj, self::DUMP, $label);
    }

    /**
     * Sets the processorURL for the FirePHP Firefox Extension.
     *
     * "The purpose of the Processor is to take the data and manipulate
     *  it after each request is complete. [...] The default Processor
     *  inserts log data into the Firebug Console."
     *
     * @param  string  $URL
     * @link   http://www.firephp.org/Wiki/Reference/CustomizeDisplay
     */
    public function setProcessorUrl($URL)
    {
        $this->headers['X-FirePHP-ProcessorURL'] = $URL;
    }

    /**
     * Sets the rendererURL for the FirePHP Firefox Extension.
     *
     * "The purpose of the Renderer is to generate the HTML to be inserted into
     *  the "Server" tab of requests in the Firebug Console and Net tabs.
     *  The Default Renderer that ships with FirePHP takes the debug data
     *  and generates a variable tree similar to the print_r() function in PHP.
     *
     * @param  string  $URL
     */
    public function setRendererUrl($URL)
    {
        $this->headers['X-FirePHP-RendererURL'] = $URL;
    }

    /**
     * Gets the headers for FirePHP as array or as json.
     *
     * @param   bool  $asJSON
     * @return  array<string,string>|string
     */
    public function getHeaders($asJSON = false)
    {
        ksort($this->headers);
        $headers = ($asJSON === true) ? join($this->headers) : $this->headers;
        return $headers;
    }
}
?>