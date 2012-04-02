<?php
/**
 * Filter that requires no argument for its constructor.
 *
 * @package     stubbles_test
 * @subpackage  filterprovider
 * @version     $Id: FilterWithoutConstArgs.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter');
/**
 * Filter that requires no argument for its constructor.
 *
 * @package     stubbles_test
 * @subpackage  filterprovider
 */
class FilterWithoutConstArgs extends stubBaseObject implements stubFilter
{
    /**
     * does the filtering
     *
     * @param   string  $value
     * @return  string
     */
    public function execute($value)
    {
         return $value;
    }
}
?>