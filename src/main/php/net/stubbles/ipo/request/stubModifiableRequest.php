<?php
/**
 * Permits modifiying request values.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubModifiableRequest.php 2678 2010-08-23 21:03:57Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest');
/**
 * Permits modifiying request values.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 */
interface stubModifiableRequest extends stubRequest
{
    /**
     * modifies a param value
     *
     * @param   string                 $paramName  name of param to modify
     * @param   string                 $value      new value for param to modify
     * @return  stubModifiableRequest
     */
    public function setParam($paramName, $value);

    /**
     * removes a param value
     *
     * @param   string                 $paramName  name of param to remove
     * @return  stubModifiableRequest
     */
    public function removeParam($paramName);

    /**
     * modifies a header value
     *
     * @param   string                 $headerName  name of header to modify
     * @param   string                 $value       new value for header to modify
     * @return  stubModifiableRequest
     * @since   1.3.0
     */
    public function setHeader($headerName, $value);

    /**
     * removes a header value
     *
     * @param   string                 $headerName  name of header to remove
     * @return  stubModifiableRequest
     * @since   1.3.0
     */
    public function removeHeader($headerName);

    /**
     * modifies a cookie value
     *
     * @param   string                 $cookieName  name of cookie to modify
     * @param   string                 $value       new value for cookie to modify
     * @return  stubModifiableRequest
     * @since   1.3.0
     */
    public function setCookie($cookieName, $value);

    /**
     * removes a cookie value
     *
     * @param   string                 $cookieName  name of cookie to remove
     * @return  stubModifiableRequest
     * @since   1.3.0
     */
    public function removeCookie($cookieName);
}
?>