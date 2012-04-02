<?php
/**
 * Class for connections to URLs of HTTP/HTTPS.
 *
 * @package     stubbles
 * @subpackage  peer_http
 * @version     $Id: stubHTTPConnection.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubHeaderList',
                      'net::stubbles::peer::http::stubHTTPRequest',
                      'net::stubbles::peer::http::stubHTTPResponse'
);
/**
 * Class for connections to URLs of HTTP/HTTPS.
 *
 * @package     stubbles
 * @subpackage  peer_http
 */
class stubHTTPConnection extends stubBaseObject
{
    /**
     * request object to open connection
     *
     * @var  stubHTTPURLContainer
     */
    protected $url      = null;
    /**
     * contains request headers
     *
     * @var  stubHeaderList
     */
    protected $headers  = null;
    /**
     * timeout
     *
     * @var  int
     */
    protected $timeout  = 30;
    /**
     * end-of-line marker
     */
    const END_OF_LINE   = "\r\n";

    /**
     * constructor
     *
     * @param  stubHTTPURLContainer  $http     url to create connection to
     * @param  stubHeaderList        $headers  optional  list of headers to be used
     */
    public function __construct(stubHTTPURLContainer $http, stubHeaderList $headers = null)
    {
        $this->url = $http;
        if (null === $headers) {
            $this->headers = new stubHeaderList();
        } else {
            $this->headers = $headers;
        }
    }

    /**
     * returns list of headers
     *
     * @return  stubHeaderList
     */
    public function getHeaderList()
    {
        return $this->headers;
    }

    /**
     * set timeout for connection
     *
     * @param   int                 $timeout  timeout for connection in seconds
     * @return  stubHTTPConnection
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * returns timeout for connection
     *
     * @return  int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * do the request with the given user agent header
     *
     * @param   string              $userAgent
     * @return  stubHTTPConnection
     */
    public function asUserAgent($userAgent)
    {
        $this->headers->putUserAgent($userAgent);
        return $this;
    }

    /**
     * say the connection was refered from given url
     *
     * @param   string              $referer
     * @return  stubHTTPConnection
     */
    public function referedFrom($referer)
    {
        $this->headers->putReferer($referer);
        return $this;
    }

    /**
     * add some cookie data to the request
     *
     * @param   array<string,string>  $cookieValues  list of key-value pairs
     * @return  stubHTTPConnection
     */
    public function withCookie(array $cookieValues)
    {
        $this->headers->putCookie($cookieValues);
        return $this;
    }

    /**
     * authorize with given credentials
     *
     * @param   string              $user
     * @param   string              $password
     * @return  stubHTTPConnection
     */
    public function authorizedAs($user, $password)
    {
        $this->headers->putAuthorization($user, $password);
        return $this;
    }

    /**
     * adds any arbitrary header
     *
     * @param   string             $key    name of header
     * @param   string             $value  value of header
     * @return  stubHTTPConnection
     */
    public function usingHeader($key, $value)
    {
        $this->headers->put($key, $value);
        return $this;
    }

    /**
     * returns response object for given URL after GET request
     *
     * @param   string            $version  optional  HTTP version
     * @return  stubHTTPResponse
     */
    public function get($version = null)
    {
        return $this->createRequest()->get($version);
    }

    /**
     * returns response object for given URL after HEAD request
     *
     * @param   string            $version  optional  HTTP version
     * @return  stubHTTPResponse
     */
    public function head($version = null)
    {
        return $this->createRequest()->head($version);
    }

    /**
     * returns response object for given URL after POST request
     *
     * @param   array<string,string>  $postValues  post data to send with POST request
     * @param   string                $version     optional  HTTP version
     * @return  stubHTTPResponse
     */
    public function post(array $postValues, $version = null)
    {
        return $this->createRequest()->preparePost($postValues)->post($version);
    }

    /**
     * helper method to create the request
     *
     * @return  stubHTTPRequest
     */
    // @codeCoverageIgnoreStart
    protected function createRequest()
    {
        return new stubHTTPRequest($this->url, $this->headers, $this->timeout);
    }
    // @codeCoverageIgnoreEnd
}
?>