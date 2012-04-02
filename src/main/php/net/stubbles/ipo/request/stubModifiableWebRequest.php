<?php
/**
 * Permits modifiying request values.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubModifiableWebRequest.php 2678 2010-08-23 21:03:57Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubModifiableRequest',
                      'net::stubbles::ipo::request::stubWebRequest'
);
/**
 * Permits modifiying request values.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 */
class stubModifiableWebRequest extends stubWebRequest implements stubModifiableRequest
{
    /**
     * modifies a param value
     *
     * @param   string                 $paramName  name of param to modify
     * @param   string                 $value      new value for param to modify
     * @return  stubModifiableRequest
     */
    public function setParam($paramName, $value)
    {
        $this->unsecureParams[$paramName] = $value;
        return $this;
    }

    /**
     * removes a param value
     *
     * @param   string                 $paramName  name of param to remove
     * @return  stubModifiableRequest
     */
    public function removeParam($paramName)
    {
        if (isset($this->unsecureParams[$paramName]) === true) {
            unset($this->unsecureParams[$paramName]);
        }

        return $this;
    }

    /**
     * modifies a header value
     *
     * @param   string                 $headerName  name of header to modify
     * @param   string                 $value       new value for header to modify
     * @return  stubModifiableRequest
     * @since   1.3.0
     */
    public function setHeader($headerName, $value)
    {
        $this->unsecureHeaders[$headerName] = $value;
        return $this;
    }

    /**
     * removes a header value
     *
     * @param   string                 $headerName  name of header to remove
     * @return  stubModifiableRequest
     * @since   1.3.0
     */
    public function removeHeader($headerName)
    {
        if (isset($this->unsecureHeaders[$headerName]) === true) {
            unset($this->unsecureHeaders[$headerName]);
        }
        
        return $this;
    }

    /**
     * modifies a cookie value
     *
     * @param   string                 $cookieName  name of cookie to modify
     * @param   string                 $value       new value for cookie to modify
     * @return  stubModifiableRequest
     * @since   1.3.0
     */
    public function setCookie($cookieName, $value)
    {
        $this->unsecureCookies[$cookieName] = $value;
        return $this;
    }

    /**
     * removes a cookie value
     *
     * @param   string                 $cookieName  name of cookie to remove
     * @return  stubModifiableRequest
     * @since   1.3.0
     */
    public function removeCookie($cookieName)
    {
        if (isset($this->unsecureCookies[$cookieName]) === true) {
            unset($this->unsecureCookies[$cookieName]);
        }

        return $this;
    }
}
?>