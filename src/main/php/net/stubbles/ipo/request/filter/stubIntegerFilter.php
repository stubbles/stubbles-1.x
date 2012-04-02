<?php
/**
 * Basic class for filters on request variables of type integer.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubIntegerFilter.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter');
/**
 * Basic class for filters on request variables of type integer.
 *
 * This filter takes any value and casts it to int.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubIntegerFilter extends stubBaseObject implements stubFilter
{
    /**
     * checks if given value is an integer
     *
     * @param   mixed  $value  value to filter
     * @return  int
     */
    function execute($value)
    {
        if (null !== $value) {
            settype($value, 'integer');
        }
        
        return $value;
    }
}
?>