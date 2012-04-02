<?php
/**
 * Basic class for filters on request variables of type boolean.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubBoolFilter.php 2506 2010-03-01 14:28:18Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter');
/**
 * Basic class for filters on request variables of type boolean.
 *
 * If given value is 1 (int or string), 'true' (string) or true (boolean) the
 * filter returns boolean true; and boolean false in all other cases.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @since       1.2.0
 */
class stubBoolFilter extends stubBaseObject implements stubFilter
{
    /**
     * checks if given value is an integer
     *
     * @param   mixed  $value  value to filter
     * @return  bool
     */
    function execute($value)
    {
        if (in_array($value, array(1, '1', 'true', true), true) === true) {
            return true;
        }
        
        return false;
    }
}
?>