<?php
/**
 * Interface for handling request variables.
 * 
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubRequest.php 2678 2010-08-23 21:03:57Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubFilteringRequestValue',
                      'net::stubbles::ipo::request::stubRequestValueErrorCollection',
                      'net::stubbles::ipo::request::stubValidatingRequestValue'
);
/**
 * Interface for handling request variables.
 * 
 * The request contains all data send by the user-agent: parameters,
 * headers and cookies. It allows to retrieve this values via validators
 * and filters. Errors that occurred during filtering are collected as well.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @see         http://stubbles.net/wiki/Docs/Validators
 */
interface stubRequest extends stubObject
{
    /**
     * request source: cookies
     */
    const SOURCE_COOKIE      = 1;
    /**
     * request source: header
     */
    const SOURCE_HEADER      = 2;
    /**
     * request source: parameters
     */
    const SOURCE_PARAM       = 4;
    /**
     * request source: body
     *
     * @since  1.3.0
     */
    const SOURCE_BODY        = 8;

    /**
     * checks if requestor accepts cookies
     *
     * @return  bool
     */
    public function acceptsCookies();

    /**
     * checks whether a request param is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasParam($paramName);

    /**
     * checks whether a request header is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasHeader($headerName);

    /**
     * checks whether a request cookie is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasCookie($cookieName);

    /**
     * returns error collection for request parameters
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function paramErrors();

    /**
     * returns error collection for request headers
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function headerErrors();

    /**
     * returns error collection for request cookies
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function cookieErrors();

    /**
     * returns error collection for request body
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function bodyErrors();

    /**
     * cancels the request, e.g. if it was detected that it is invalid
     */
    public function cancel();

    /**
     * checks whether the request has been cancelled or not
     *
     * @return  bool
     */
    public function isCancelled();

    /**
     * returns the request method
     *
     * @return  string
     */
    public function getMethod();

    /**
     * returns the uri of the request
     * 
     * @return  string
     */
    public function getURI();

    /**
     * returns complete uri including scheme
     *
     * @return  string
     * @since   1.3.0
     */
    public function getCompleteUri();

    /**
     * checks whether a request value from parameters is valid or not
     *
     * @param   string                      $paramName  name of request value
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateParam($paramName);

    /**
     * checks whether a request value from headers is valid or not
     *
     * @param   string                      $headerName  name of header
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateHeader($headerName);

    /**
     * checks whether a request value from cookie is valid or not
     *
     * @param   string                      $cookieName  name of cookie
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateCookie($cookieName);

    /**
     * checks whether a request body is valid or not
     *
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateBody();

    /**
     * returns request value from params for filtering or validation
     *
     * @param   string                     $paramName  name of request value
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readParam($paramName);

    /**
     * returns request value from headers for filtering or validation
     *
     * @param   string                     $headerName  name of header
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readHeader($headerName);

    /**
     * returns request value from cookies for filtering or validation
     *
     * @param   string                     $cookieName  name of cookie
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readCookie($cookieName);

    /**
     * returns request body for filtering or validation
     *
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readBody();

    /**
     * return an array of all param names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getParamNames();

    /**
     * return an array of all header names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getHeaderNames();

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getCookieNames();
}
?>