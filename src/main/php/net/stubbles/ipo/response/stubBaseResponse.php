<?php
/**
 * Base class for a response to a request.
 *
 * @package     stubbles
 * @subpackage  ipo_response
 * @version     $Id: stubBaseResponse.php 3106 2011-03-23 17:44:53Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Base class for a response to a request.
 *
 * This class can be used for responses in web environments. It
 * collects all data of the response and is able to send it back
 * to the source that initiated the request.
 *
 * @package     stubbles
 * @subpackage  ipo_response
 */
class stubBaseResponse extends stubBaseObject implements stubResponse
{
    /**
     * map of status codes to reason phrases
     *
     * @var  array<int,string>
     */
    protected static $reasonPhrases = array(100 => 'Continue',
                                            101 => 'Switching Protocols',
                                            200 => 'OK',
                                            201 => 'Created',
                                            202 => 'Accepted',
                                            203 => 'Non-Authoritative Information',
                                            204 => 'No Content',
                                            205 => 'Reset Content',
                                            206 => 'Partial Content',
                                            300 => 'Multiple Choices',
                                            301 => 'Moved Permanently',
                                            302 => 'Found',
                                            303 => 'See Other',
                                            304 => 'Not Modified',
                                            305 => 'Use Proxy',
                                            307 => 'Temporary Redirect',
                                            400 => 'Bad Request',
                                            401 => 'Unauthorized',
                                            402 => 'Payment Required',
                                            403 => 'Forbidden',
                                            404 => 'Not Found',
                                            405 => 'Method Not Allowed',
                                            406 => 'Not Acceptable',
                                            407 => 'Proxy Authentication Required',
                                            408 => 'Request Timeout',
                                            409 => 'Conflict',
                                            410 => 'Gone',
                                            411 => 'Length Required',
                                            412 => 'Precondition Failed',
                                            413 => 'Request Entity Too Large',
                                            414 => 'Request-URI Too Long',
                                            415 => 'Unsupported Media Type',
                                            416 => 'Requested Range Not Satisfiable',
                                            417 => 'Expectation Failed',
                                            500 => 'Internal Server Error',
                                            501 => 'Not Implemented',
                                            502 => 'Bad Gateway',
                                            503 => 'Service Unavailable',
                                            504 => 'Gateway Timeout',
                                            505 => 'HTTP Version Not Supported'
                                      );
    /**
     * current php sapi
     *
     * @var  string
     */
    protected $sapi;
    /**
     * http version to be used
     *
     * @var  string
     */
    protected $version;
    /**
     * status code to be send
     *
     * @var  int
     */
    protected $statusCode    = 200;
    /**
     * status message to be send
     *
     * @var  string
     */
    protected $reasonPhrase  = 'OK';
    /**
     * list of headers for this response
     *
     * @var  array<string,string>
     */
    protected $headers       = array();
    /**
     * list of cookies for this response
     *
     * @var  array<string,stubCookie>
     */
    protected $cookies       = array();
    /**
     * data to send as body of response
     *
     * @var  string
     */
    protected $body;

    /**
     * constructor
     *
     * @param  string  $version  optional  http version      should be a string like '1.0' or '1.1'
     * @param  string  $sapi     optional  current php sapi
     */
    public function __construct($version = '1.1', $sapi = PHP_SAPI)
    {
        $this->version = $version;
        $this->sapi    = $sapi;
    }

    /**
     * merges other response into this instance
     *
     * All values of the current instance will be overwritten by the other
     * instance. However, merging does not change the http version of this
     * response instance. Cookies and headers which are present in this instance
     * but not in the other instance will be kept.
     *
     * @param   stubResponse  $other
     * @return  stubResponse
     * @since   1.7.0
     */
    public function merge(stubResponse $other)
    {
        $this->setStatusCode($other->getStatusCode());
        foreach ($other->getHeaders() as $name => $value) {
            $this->addHeader($name, $value);
        }

        foreach ($other->getCookies() as $cookie) {
            $this->addCookie($cookie);
        }

        $this->clearBody()->write($other->getBody());
        return $this;
    }

    /**
     * clears the response
     *
     * @return  stubResponse
     */
    public function clear()
    {
        $this->setStatusCode(200);
        $this->headers = array();
        $this->cookies = array();
        $this->body    = null;
        return $this;
    }

    /**
     * sets the http version
     *
     * The version should be a string like '1.0' or '1.1'.
     *
     * @param   string        $version
     * @return  stubResponse
     * @deprecated  will be removed with 1.8 or 2.0
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * returns the http version
     *
     * @return  string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * sets the status code to be send
     *
     * This needs only to be done if another status code then the default one
     * 200 Found should be send.
     *
     * The reason phrase is optional. If none given it will use the default
     * reason phrase according to the HTTP specification.
     *
     * @param   int           $statusCode
     * @param   string        $reasonPhrase  optional
     * @return  stubResponse
     * @throws  stubIllegalArgumentException
     */
    public function setStatusCode($statusCode, $reasonPhrase = null)
    {
        if (null == $statusCode) {
            $this->statusCode = null;
            return $this;
        }
        
        if (isset(self::$reasonPhrases[$statusCode]) === false) {
            throw new stubIllegalArgumentException('Given status code ' . $statusCode . ' is not a valid HTTP status code.');
        }

        $this->statusCode = $statusCode;
        if (null == $reasonPhrase) {
            $this->reasonPhrase = self::$reasonPhrases[$statusCode];
        } else {
            $this->reasonPhrase = $reasonPhrase;
        }

        return $this;
    }

    /**
     * returns status code to be send
     *
     * If return value is <null> the default one will be send.
     *
     * @return  int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * add a header to the response
     *
     * @param   string        $name   the name of the header
     * @param   string        $value  the value of the header
     * @return  stubResponse
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * returns the list of headers
     *
     * @return  array<string,string>
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * checks if header with given name is set
     *
     * @param   string  $name
     * @return  bool
     * @since   1.5.0
     */
    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    /**
     * returns header with given name
     *
     * If header with given name does not exist return value is null.
     *
     * @param   string  $name
     * @return  string
     * @since   1.5.0
     */
    public function getHeader($name)
    {
        if ($this->hasHeader($name) === true) {
            return $this->headers[$name];
        }

        return null;
    }

    /**
     * add a cookie to the response
     *
     * @param   stubCookie    $cookie  the cookie to set
     * @return  stubResponse
     */
    public function addCookie(stubCookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
        return $this;
    }

    /**
     * add a cookie to the response
     *
     * @param   stubCookie    $cookie  the cookie to set
     * @return  stubResponse
     * @deprecated  use addCookie() instead
     */
    public function setCookie(stubCookie $cookie)
    {
        return $this->addCookie($cookie);
    }

    /**
     * returns the list of cookies
     *
     * @return  array<string,stubCookie>
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * checks if cookie with given name is set
     *
     * @param   string  $name
     * @return  bool
     * @since   1.5.0
     */
    public function hasCookie($name)
    {
        return isset($this->cookies[$name]);
    }

    /**
     * returns cookie with given name
     *
     * If cookie with given name does not exist return value is null.
     *
     * @param   string      $name
     * @return  stubCookie
     * @since   1.5.0
     */
    public function getCookie($name)
    {
        if ($this->hasCookie($name) === true) {
            return $this->cookies[$name];
        }

        return null;
    }

    /**
     * write data into the response
     *
     * @param   string        $body
     * @return  stubResponse
     */
    public function write($body)
    {
        $this->body .= $body;
        return $this;
    }

    /**
     * returns the data written so far
     *
     * @return  string
     * @deprecated  use getBody() instead, will be removed with 2.0.0
     */
    public function getData()
    {
        return $this->getBody();
    }

    /**
     * returns the data written so far
     *
     * @return  string
     * @since   1.7.0
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * replaces the data written so far with the new data
     *
     * @param   string        $data
     * @return  stubResponse
     * @deprecated  use replaceBody() instead, will be removed with 2.0.0
     */
    public function replaceData($data)
    {
        return $this->replaceBody($data);
    }

    /**
     * replaces the data written so far with the new data
     *
     * @param   string        $body
     * @return  stubResponse
     * @since   1.7.0
     */
    public function replaceBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * removes data completely
     *
     * @return  stubResponse
     * @deprecated  use clearBody() instead, will be removed with 2.0.0
     */
    public function clearData()
    {
        return $this->clearBody();
    }

    /**
     * removes data completely
     *
     * @return  stubResponse
     * @since   1.7.0
     */
    public function clearBody()
    {
        $this->body = null;
        return $this;
    }

    /**
     * creates a Location header which causes a redirect when the response is send
     *
     * Status code is optional, default is 302.
     *
     * The reason phrase is optional. If none given it will use the default
     * reason phrase for the given status code according to the HTTP specification.
     *
     * @param   string        $url           url to redirect to
     * @param   int           $statusCode    optional HTTP status code to redirect with (301, 302, ...)
     * @param   string        $reasonPhrase  optional HTTP status code reason phrase
     * @return  stubBaseResponse
     * @since   1.3.0
     */
    public function redirect($url, $statusCode = 302, $reasonPhrase = null)
    {
        $this->addHeader('Location', $url);
        $this->setStatusCode($statusCode, $reasonPhrase);
        return $this;
    }

    /**
     * send the response out
     *
     * @return  stubResponse
     */
    public function send()
    {
        if (null !== $this->statusCode) {
            if ('cgi' === $this->sapi) {
                $this->header('Status: ' . $this->statusCode . ' ' . $this->reasonPhrase);
            } else {
                $this->header('HTTP/' . $this->version . ' ' . $this->statusCode . ' ' . $this->reasonPhrase);
            }
        }

        foreach ($this->headers as $name => $value) {
            $this->header($name . ': ' . $value);
        }

        foreach ($this->cookies as $cookie) {
            $cookie->send();
        }

        if (null != $this->body) {
            $this->sendBody($this->body);
        }

        return $this;
    }

    /**
     * helper method to send the header
     *
     * @param  string  $header
     */
    protected function header($header)
    {
        header($header);
    }

    /**
     * helper method to send the body
     *
     * @param  string  $body
     */
    protected function sendBody($body)
    {
        echo $body;
        flush();
    }
}
?>