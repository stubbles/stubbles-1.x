<?php
/**
 * ReST result formatters for JSON.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @version     $Id: stubJsonFormatter.php 2399 2009-12-01 13:14:07Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubErrorFormatter',
                      'net::stubbles::service::rest::format::stubFormatter'
);
/**
 * ReST result formatters for JSON.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @since       1.1.0
 */
class stubJsonFormatter extends stubBaseObject implements stubFormatter, stubErrorFormatter
{
    /**
     * returns content type of formatted result
     *
     * @return  string
     */
    public function getContentType()
    {
        return 'application/json';
    }

    /**
     * formats result for response
     *
     * @param   mixed   $result
     * @return  string
     */
    public function format($result)
    {
        return json_encode($result);
    }

    /**
     * write error message about 404 Not Found error
     *
     * @return  string
     */
    public function formatNotFoundError()
    {
        return json_encode(array('error' => 'Given resource could not be found.'));
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
        return json_encode(array('error' => 'The given request method ' . strtoupper($requestMethod) . ' is not valid. Please use ' . join(', ', $allowedMethods) . '.'));
    }

    /**
     * write error message about 500 Internal Server error
     *
     * @param   Exception  $e
     * @return  string
     */
    public function formatInternalServerError(Exception $e)
    {
        return json_encode(array('error' => 'Internal Server Error: ' . $e->getMessage()));
    }
}
?>