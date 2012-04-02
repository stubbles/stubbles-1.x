<?php
/**
 * Interface for a response to a request.
 *
 * @package     stubbles
 * @subpackage  ipo_response
 * @version     $Id: stubResponse.php 3106 2011-03-23 17:44:53Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubCookie');
/**
 * Interface for a response to a request.
 * 
 * The response collects all data that should be send to the source
 * that initiated the request.
 *
 * @package     stubbles
 * @subpackage  ipo_response
 */
interface stubResponse extends stubObject
{
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
    public function merge(stubResponse $other);

    /**
     * clears the response
     *
     * @return  stubResponse
     */
    public function clear();

    /**
     * sets the http version
     *
     * The version should be a string like '1.0' or '1.1'.
     *
     * @param   string        $version
     * @return  stubResponse
     * @deprecated  will be removed with 1.8 or 2.0
     */
    public function setVersion($version);

    /**
     * returns the http version
     *
     * @return  string
     */
    public function getVersion();

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
     */
    public function setStatusCode($statusCode, $reasonPhrase = null);

    /**
     * returns status code to be send
     *
     * If return value is <null> the default one will be send.
     *
     * @return  int
     */
    public function getStatusCode();

    /**
     * add a header to the response
     *
     * @param   string        $name   the name of the header
     * @param   string        $value  the value of the header
     * @return  stubResponse
     */
    public function addHeader($name, $value);

    /**
     * returns the list of headers
     *
     * @return  array<string,string>
     */
    public function getHeaders();

    /**
     * checks if header with given name is set
     *
     * @param   string  $name
     * @return  bool
     * @since   1.5.0
     */
    public function hasHeader($name);

    /**
     * returns header with given name
     *
     * If header with given name does not exist return value is null.
     *
     * @param   string  $name
     * @return  string
     * @since   1.5.0
     */
    public function getHeader($name);

    /**
     * add a cookie to the response
     *
     * @param   stubCookie    $cookie  the cookie to set
     * @return  stubResponse
     */
    public function addCookie(stubCookie $cookie);

    /**
     * add a cookie to the response
     *
     * @param   stubCookie    $cookie  the cookie to set
     * @return  stubResponse
     * @deprecated  use addCookie() instead
     */
    public function setCookie(stubCookie $cookie);

    /**
     * returns the list of cookies
     *
     * @return  array<string,stubCookie>
     */
    public function getCookies();

    /**
     * checks if cookie with given name is set
     *
     * @param   string  $name
     * @return  bool
     * @since   1.5.0
     */
    public function hasCookie($name);

    /**
     * returns cookie with given name
     *
     * If cookie with given name does not exist return value is null.
     *
     * @param   string      $name
     * @return  stubCookie
     * @since   1.5.0
     */
    public function getCookie($name);

    /**
     * write body into the response
     *
     * @param   string        $body
     * @return  stubResponse
     */
    public function write($body);

    /**
     * returns the data written so far
     * 
     * @return  string
     * @deprecated  use getBody() instead, will be removed with 2.0.0
     */
    public function getData();

    /**
     * returns the data written so far
     *
     * @return  string
     * @since   1.7.0
     */
    public function getBody();

    /**
     * replaces the data written so far with the new data
     *
     * @param   string        $data
     * @return  stubResponse
     * @deprecated  use replaceBody() instead, will be removed with 2.0.0
     */
    public function replaceData($data);

    /**
     * replaces the data written so far with the new data
     *
     * @param   string        $data
     * @return  stubResponse
     * @since   1.7.0
     */
    public function replaceBody($body);

    /**
     * removes data completely
     *
     * @return  stubResponse
     * @deprecated  use clearBody() instead, will be removed with 2.0.0
     */
    public function clearData();

    /**
     * removes data completely
     *
     * @return  stubResponse
     * @since   1.7.0
     */
    public function clearBody();

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
     * @return  stubResponse
     * @since   1.3.0
     */
    public function redirect($url, $statusCode = 302, $reasonPhrase = null);

    /**
     * send the response out
     *
     * @return  stubResponse
     */
    public function send();
}
?>