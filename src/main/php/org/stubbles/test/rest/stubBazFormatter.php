<?php
/**
 * Helper class for unit tests.
 *
 * @package     stubbles
 * @subpackage  test_rest
 * @version     $Id: stubBazFormatter.php 3204 2011-11-02 16:12:02Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubErrorFormatter',
                      'net::stubbles::service::rest::format::stubFormatter'
);
/**
 * Helper class for unit tests.
 *
 * @package     stubbles
 * @subpackage  test_rest
 */
class stubBazFormatter extends stubBaseObject implements stubFormatter, stubErrorFormatter
{
    /**
     * returns content type of formatted result
     *
     * @return  string
     */
    public function getContentType()
    {
        return 'baz';
    }

    /**
     * formats result for response
     *
     * @param   mixed   $result
     * @return  string
     */
    public function format($result)
    {
        // intentionally empty
    }

    /**
     * write error message about 404 Not Found error
     *
     * @return  string
     */
    public function formatNotFoundError()
    {
        // intentionally empty
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
        // intentionally empty
    }

    /**
     * write error message about 500 Internal Server error
     *
     * @param   Exception  $e
     * @return  string
     */
    public function formatInternalServerError(Exception $e)
    {
        // intentionally empty
    }
}
?>