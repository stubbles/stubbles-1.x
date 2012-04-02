<?php
/**
 * ReST result formatters for plain text.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @version     $Id: stubPlainTextFormatter.php 2568 2010-05-26 11:04:25Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubErrorFormatter',
                      'net::stubbles::service::rest::format::stubFormatter'
);
/**
 * ReST result formatters for JSON.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @since       1.1.2
 */
class stubPlainTextFormatter extends stubBaseObject implements stubFormatter, stubErrorFormatter
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
        if (is_object($result) === true && method_exists($result, '__toString') === true) {
            return (string) $result;
        } elseif (is_object($result) === true || is_array($result) === true) {
            return var_export($result, true);
        } elseif (is_bool($result) === true && true === $result) {
            return 'true';
        } elseif (is_bool($result) === true && false === $result) {
            return 'false';
        }

        return (string) $result;
    }

    /**
     * write error message about 404 Not Found error
     *
     * @return  string
     */
    public function formatNotFoundError()
    {
        return 'Given resource could not be found.';
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
        return 'The given request method ' . strtoupper($requestMethod) . ' is not valid. Please use ' . join(', ', $allowedMethods) . '.';
    }

    /**
     * write error message about 500 Internal Server error
     *
     * @param   Exception  $e
     * @return  string
     */
    public function formatInternalServerError(Exception $e)
    {
        return 'Internal Server Error: ' . $e->getMessage();
    }
}
?>