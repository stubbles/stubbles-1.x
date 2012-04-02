<?php
/**
 * Common base interface for formatters.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @version     $Id: stubFormatContentType.php 2399 2009-12-01 13:14:07Z mikey $
 */
/**
 * Common base interface for formatters.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @since       1.1.0
 */
interface stubFormatContentType extends stubObject
{
    /**
     * returns content type of formatted result
     *
     * @return  string
     */
    public function getContentType();
}
?>