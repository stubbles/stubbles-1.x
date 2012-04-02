<?php
/**
 * Class for filtering texts (strings containing line feeds).
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubTextFilter.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter');
/**
 * Class for filtering texts (strings containing line feeds).
 *
 * This filter removes windows line breaks and html tags from the value. Via
 * setAllowedTags() a list of allowed tags that will not be removed can be
 * specified. Use the allowed tags option very careful. It does not protect
 * you against possible XSS attacks!
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubTextFilter extends stubBaseObject implements stubFilter
{
    /**
     * list of allowed tags
     *
     * @var  array<string>
     */
    protected $allowedTags = array();

    /**
     * set the list of allowed tags
     *
     * Use this option very careful. It does not protect you against
     * possible XSS attacks!
     *
     * @param  array<string>  $allowedTags
     */
    public function setAllowedTags(array $allowedTags)
    {
        $this->allowedTags = $allowedTags;
    }

    /**
     * returns the list of allowed tags
     *
     * @return  array<string>
     */
    public function getAllowedTags()
    {
        return $this->allowedTags;
    }

    /**
     * filter strings
     *
     * @param   string  $value  value to filter
     * @return  string  filtered value
     */
    public function execute($value)
    {
        if (null != $value) {
            // remove carriage return and all added slashes from magic_gpc_quote
            $value = str_replace(chr(13), '', stripslashes($value));
            $value = strip_tags($value, ((count($this->allowedTags) > 0) ? ('<' . join('><', $this->allowedTags) . '>') : ('')));
        }

        return $value;
    }
}
?>