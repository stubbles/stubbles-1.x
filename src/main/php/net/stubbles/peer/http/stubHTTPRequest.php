<?php
/**
 * Class for sending a HTTP request.
 *
 * @package     stubbles
 * @subpackage  peer_http
 * @version     $Id: stubHTTPRequest.php 2256 2009-06-23 22:07:09Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubHeaderList',
                      'net::stubbles::peer::stubConnectionException',
                      'net::stubbles::peer::stubSocket',
                      'net::stubbles::peer::http::stubHTTPConnection',
                      'net::stubbles::peer::http::stubHTTPResponse',
                      'net::stubbles::peer::http::stubHTTPURLContainer'
);
/**
 * Class for sending a HTTP request.
 *
 * @package     stubbles
 * @subpackage  peer_http
 */
class stubHTTPRequest extends stubBaseObject
{
    /**
     * the http address to setup a connection to
     *
     * @var  stubHTTPURLContainer
     */
    protected $httpURL = null;
    /**
     * contains request headers
     *
     * @var  stubHeaderList
     */
    protected $headers = null;
    /**
     * contains body for request
     *
     * @var  string
     */
    protected $body    = '';
    /**
     * timeout
     *
     * @var  int
     */
    protected $timeout = 30;
    /**
     * request method type: GET
     */
    const METHOD_GET   = 'GET';
    /**
     * request method type: POST
     */
    const METHOD_POST  = 'POST';
    /**
     * request method type: HEAD
     */
    const METHOD_HEAD  = 'HEAD';
    /**
     * HTTP version: 1.0
     */
    const VERSION_1_0  = 'HTTP/1.0';
    /**
     * HTTP version: 1.1
     */
    const VERSION_1_1  = 'HTTP/1.1';

    /**
     * constructor
     *
     * @param  stubHTTPURLContainer  $httpURL  HTTP URL to perform a request to
     * @param  stubHeaderList        $header   list of request headers
     * @param  int                   $timeout  timeout for connection in seconds
     */
    public function __construct(stubHTTPURLContainer $httpURL, stubHeaderList $header, $timeout)
    {
        $this->httpURL = $httpURL;
        $this->headers = $header;
        $this->timeout = $timeout;
    }

    /**
     * initializes a get request
     *
     * @param   string            $version  optional  HTTP version
     * @return  stubHTTPResponse
     */
    public function get($version = null)
    {
        $socket = $this->createSocket();
        $this->processHeader($socket, self::METHOD_GET, $version);
        $socket->write(stubHTTPConnection::END_OF_LINE);
        return new stubHTTPResponse($socket);
    }

    /**
     * initializes a head request
     *
     * @param   string            $version  optional  HTTP version
     * @return  stubHTTPResponse
     */
    public function head($version = null)
    {
        $socket = $this->createSocket();
        $this->processHeader($socket, self::METHOD_HEAD, $version);
        $socket->write('Connection: close' . stubHTTPConnection::END_OF_LINE . stubHTTPConnection::END_OF_LINE);
        return new stubHTTPResponse($socket);
    }

    /**
     * creates required headers for post request and encodes post values
     *
     * @param   array<string,string>  $postValues  post values to submit
     * @return  stubHTTPRequest
     */
    public function preparePost(array $postValues)
    {
        foreach ($postValues as $key => $value) {
            $this->body .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        
        $this->headers->put('Content-Type', 'application/x-www-form-urlencoded');
        $this->headers->put('Content-Length', strlen($this->body));
        return $this;
    }

    /**
     * initializes a post request
     *
     * @param   string            $version  optional  HTTP version
     * @return  stubHTTPResponse
     */
    public function post($version = null)
    {
        $socket = $this->createSocket();
        $this->processHeader($socket, self::METHOD_POST, $version);
        $socket->write(stubHTTPConnection::END_OF_LINE);
        $socket->write($this->body);
        return new stubHTTPResponse($socket);
    }

    /**
     * creates the socket
     *
     * @return  stubSocket
     */
    protected function createSocket()
    {
        return new stubSocket($this->httpURL->getHost(),
                              $this->httpURL->getPort(),
                              (($this->httpURL->getScheme() === 'https') ? ('ssl://') : (null))
               );
    }

    /**
     * helper method to send the headers
     *
     * @param  stubSocket  $socket   the socket to write headers to
     * @param  string      $method   HTTP method
     * @param  string      $version  HTTP version
     */
    protected function processHeader(stubSocket $socket, $method, $version)
    {
        if (self::METHOD_POST != $method && self::METHOD_GET != $method && self::METHOD_HEAD != $method) {
            $method = self::METHOD_GET;
        }
        
        if (self::VERSION_1_0 != $version && self::VERSION_1_1 != $version) {
            $version = self::VERSION_1_1;
        }
        
        $socket->setTimeout($this->timeout);
        $socket->connect();
        $socket->write($method . ' ' . $this->httpURL->getPath() . ' ' . $version . stubHTTPConnection::END_OF_LINE);
        $socket->write('Host: ' . $this->httpURL->getHost() . stubHTTPConnection::END_OF_LINE);
        
        // prepare last headers and write all headers to socket
        if ($this->headers->containsKey('User-Agent') === false) {
            $this->headers->putUserAgent('stubbles HTTP Client');
        }
        
        $this->headers->enablePower();
        $this->headers->putDate();
        foreach ($this->headers as $key => $value) {
            $socket->write($key . ': ' . $value . stubHTTPConnection::END_OF_LINE);
        }
    }
}
?>