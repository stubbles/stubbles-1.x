<?php
/**
 * Interface for error formatter which writes error messages.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @version     $Id: stubErrorFormatter.php 2399 2009-12-01 13:14:07Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubFormatContentType');
/**
 * Interface for error formatter which writes error messages.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @since       1.1.0
 */
interface stubErrorFormatter extends stubFormatContentType
{
    /**
     * write error message about 404 Not Found error
     *
     * @return  string
     */
    public function formatNotFoundError();

    /**
     * write error message about 405 Method Not Allowed error
     *
     * @param   string         $requestMethod   original request method
     * @param   array<string>  $allowedMethods  list of allowed methods
     * @return  string
     */
    public function formatMethodNotAllowedError($requestMethod, array $allowedMethods);

    /**
     * write error message about 500 Internal Server error
     *
     * @param   Exception  $e
     * @return  string
     */
    public function formatInternalServerError(Exception $e);
}
?>