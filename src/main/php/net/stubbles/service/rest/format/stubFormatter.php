<?php
/**
 * Interface for ReST result formatters.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @version     $Id: stubFormatter.php 2399 2009-12-01 13:14:07Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubFormatContentType');
/**
 * Interface for ReST result formatters.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @since       1.1.0
 */
interface stubFormatter extends stubFormatContentType
{
    /**
     * formats result for response
     *
     * @param   mixed   $result
     * @return  string
     */
    public function format($result);
}
?>