<?php
/**
 * Class for filtering strings (singe line).
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubStringFilter.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter');
/**
 * Class for filtering strings (singe line).
 *
 * This filter removes all line breaks, slashes and HTML tags.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubStringFilter extends stubBaseObject implements stubFilter
{
    /**
     * filter strings
     *
     * @param   string  $value  value to filter
     * @return  string  filtered value
     */
    public function execute($value)
    {
        if (null != $value) {
            // remove line feeds, HTML and all added slashes from magic_gpc_quote
            $value = str_replace(chr(10), '', str_replace(chr(13), '', stripslashes($value)));
            $value = strip_tags($value);
        }
        
        return $value;
    }
}
?>