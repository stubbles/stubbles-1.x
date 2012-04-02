<?php
/**
 * Class for reading a HTTP response.
 *
 * @package     stubbles
 * @subpackage  peer_http
 * @version     $Id: stubHTTPResponse.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalAccessException',
                      'net::stubbles::peer::stubHeaderList',
                      'net::stubbles::peer::stubConnectionException',
                      'net::stubbles::peer::stubSocket'
);
/**
 * Class for reading a HTTP response.
 *
 * @package     stubbles
 * @subpackage  peer_http
 */
class stubHTTPResponse extends stubBaseObject
{
    /**
     * the socket we read the response from
     *
     * @var  stubSocket
     */
    protected $socket               = null;
    /**
     * list of response parts
     *
     * @var  array<string,scalar>
     */
    protected $response             = array();
    /**
     * contains headers of response
     *
     * @var  stubHeaderList
     */
    protected $headers              = null;
    /**
     * contains body of response
     *
     * @var  string
     */
    protected $body                 = '';
    /**
     * response type data: status line
     */
    const TYPE_STATUS_LINE          = 'Status-Line';
    /**
     * response type data: HTTP version
     */
    const TYPE_HTTP_VERSION         = 'HTTP-Version';
    /**
     * response type data: status code
     */
    const TYPE_STATUS_CODE          = 'Status-Code';
    /**
     * response type data: status class
     */
    const TYPE_STATUS_CLASS         = 'Status-Class';
    /**
     * response type data: reason phrase
     */
    const TYPE_REASON_PHRASE        = 'Reason-Phrase';
    /**
     * response status class: informational (100-199)
     */
    const STATUS_CLASS_INFO         = 'Informational';
    /**
     * response status class: successful request (200-299)
     */
    const STATUS_CLASS_SUCCESS      = 'Success';
    /**
     * response status class: redirection (300-399)
     */
    const STATUS_CLASS_REDIRECT     = 'Redirection';
    /**
     * response status class: errors by client (400-499)
     */
    const STATUS_CLASS_ERROR_CLIENT = 'Client Error';
    /**
     * response status class: errors on server (500-599)
     */
    const STATUS_CLASS_ERROR_SERVER = 'Server Error';
    /**
     * response status class: unknown status code
     */
    const STATUS_CLASS_UNKNOWN      = 'Unknown';

    /**
     * constructor
     *
     * @param  stubSocket  $socket  socket where the response can be read from
     */
    public function __construct(stubSocket $socket)
    {
        $this->socket = $socket;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->socket->disconnect();
    }

    /**
     * returns the used socket
     *
     * @return  stubSocket
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * read the response from socket and parse it
     *
     * @return  stubHTTPResponse
     */
    public function read()
    {
        return $this->readHeader()->readBody();
    }

    /**
     * reads response headers
     *
     * @return  stubHTTPResponse
     */
    public function readHeader()
    {
        $this->parseHead($this->socket->readLine());
        $header = '';
        $line   = '';
        while ($this->socket->eof() === false && stubHTTPConnection::END_OF_LINE !== $line) {
            $line    = $this->socket->read();
            $header .= $line;
        }

        $this->headers = stubHeaderList::fromString($header);
        return $this;
    }

    /**
     * reads the response body
     *
     * @return  stubHTTPResponse
     * @throws  stubIllegalAccessException
     */
    public function readBody()
    {
        if (null === $this->headers) {
            throw new stubIllegalAccessException('Need to read response headers first.');
        }

        if ($this->headers->get('Transfer-Encoding') === 'chunked') {
            $this->body = $this->readChunked();
        } else {
            $this->body = $this->readDefault($this->headers->get('Content-Length', 4096));
        }

        return $this;
    }

    /**
     * helper method to read chunked response body
     *
     * @return  string
     */
    protected function readChunked()
    {
        // it gets a little bit more complicated because we can not read the data in a whole
        // the following lines implement the pseudo code given in RFC 2616 section 19.4.6: Introduction of Transfer-Encoding
        $readLength = 0;
        $chunksize  = null;
        $extension  = null;
        $body       = '';
        // read chunk-size, chunk-extension (if any) and CRLF
        sscanf($this->socket->read(1024), "%x%s\r\n", $chunksize, $extension);
        while (0 < $chunksize) {
            // read chunk-data and CRLF
            $data        = $this->socket->readBinary($chunksize + 2);
            // append chunk-data to entity-body
            $body       .= rtrim($data);
            $readLength += $chunksize;
            // read chunk-size and CRLF
            sscanf($this->socket->read(1024), "%x\r\n", $chunksize);
        }

        #read entity-header
        #while (entity-header not empty) {
        #    append entity-header to existing header fields
        #    read entity-header
        #}

        // set correct content length
        $this->headers->put('Content-Length', $readLength);
        // remove "chunked" from Transfer-Encoding
        $this->headers->remove('Transfer-Encoding');
        return $body;
    }

    /**
     * helper method for default reading of response body
     *
     * @param   int     $readLength  expected length of response body
     * @return  string
     */
    protected function readDefault($readLength)
    {
        $body = $buffer = '';
        $read = 0;

        while ($read < $readLength) {
            $buffer  = $this->socket->read($readLength);
            $read   += strlen($buffer);
            $body   .= $buffer;
        }

        return $body;
    }

    /**
     * returns response data of requested type
     *
     * @param   string  $type  type of response data
     * @return  string
     */
    public function getType($type)
    {
        if (isset($this->response[$type]) === true) {
            return $this->response[$type];
        }

        return null;
    }

    /**
     * returns list of available response types
     *
     * @return  array<string>
     */
    public function getTypes()
    {
        static $types = array();
        if (count($types) === 0) {
            $refClass  = new ReflectionClass(__CLASS__);
            $constants = $refClass->getConstants();
            foreach (array_keys($constants) as $name) {
                if (strstr($name, 'TYPE_') !== false) {
                    $types[] = $name;
                }
            }
        }

        return $types;
    }

    /**
     * returns list of headers from response
     *
     * @return  stubHeaderList
     */
    public function getHeader()
    {
        return $this->headers;
    }

    /**
     * returns body of response
     *
     * @return  string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * returns status code of response
     *
     * @return  int
     */
    public function getStatusCode()
    {
        return (int) $this->getType(self::TYPE_STATUS_CODE);
    }

    /**
     * parses first line of response
     *
     * @param  string  $head  first line of response
     */
    protected function parseHead($head)
    {
        $matches = array();
        if (preg_match('=^(HTTP/\d+\.\d+) (\d{3}) ([^' . stubHTTPConnection::END_OF_LINE . ']*)=', $head, $matches) == false) {
            return;
        }

        $this->response[self::TYPE_STATUS_LINE]   = $matches[0];
        $this->response[self::TYPE_HTTP_VERSION]  = $matches[1];
        $this->response[self::TYPE_STATUS_CODE]   = $matches[2];
        $this->response[self::TYPE_REASON_PHRASE] = $matches[3];
        switch ($this->response[self::TYPE_STATUS_CODE][0]) {
            case 1:
                $this->response[self::TYPE_STATUS_CLASS] = self::STATUS_CLASS_INFO;
                break;

            case 2:
                $this->response[self::TYPE_STATUS_CLASS] = self::STATUS_CLASS_SUCCESS;
                break;

            case 3:
                $this->response[self::TYPE_STATUS_CLASS] = self::STATUS_CLASS_REDIRECT;
                break;

            case 4:
                $this->response[self::TYPE_STATUS_CLASS] = self::STATUS_CLASS_ERROR_CLIENT;
                break;

            case 5:
                $this->response[self::TYPE_STATUS_CLASS] = self::STATUS_CLASS_ERROR_SERVER;
                break;

            default:
                $this->response[self::TYPE_STATUS_CLASS] = self::STATUS_CLASS_UNKNOWN;
        }
    }
}
?>