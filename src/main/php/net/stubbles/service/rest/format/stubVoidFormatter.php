<?php
/**
 * ReST result formatters which returns empty content.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @version     $Id: stubVoidFormatter.php 2436 2010-01-05 16:38:36Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubErrorFormatter',
                      'net::stubbles::service::rest::format::stubFormatter'
);
/**
 * ReST result formatters which returns empty content.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @since       1.1.0
 */
class stubVoidFormatter extends stubBaseObject implements stubFormatter, stubErrorFormatter
{
    /**
     * returns content type of formatted result
     *
     * @return  string
     */
    public function getContentType()
    {
        return 'text/plain';
    }

    /**
     * formats result for response
     *
     * @param   mixed   $result
     * @return  string
     */
    public function format($result)
    {
        return '';
    }

    /**
     * write error message about 404 Not Found error
     *
     * @return  string
     */
    public function formatNotFoundError()
    {
        return '';
    }

    /**
     * write error message about 405 Method Not Allowed error
     *
     * @param   string         $requestMethod   original request method
     * @param   array<string>  $allowedMethods  list of allowed methods
     * @return  string
     */
    public function formatMethodNotAllowedError($requestMethod, array $allowedMethods)
    {
        return '';
    }

    /**
     * write error message about 500 Internal Server error
     *
     * @param   Exception  $e
     * @return  string
     */
    public function formatInternalServerError(Exception $e)
    {
        return '';
    }
}
?>