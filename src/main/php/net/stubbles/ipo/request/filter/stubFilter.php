<?php
/**
 * Interface for filters.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubFilter.php 2329 2009-09-16 16:00:05Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilterException');
/**
 * Interface for filter.
 * 
 * Filters can be used to take request values, validate them and change them
 * into any other value.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
interface stubFilter extends stubObject
{
    /**
     * execute the filter
     *
     * @param   mixed                $value  value to filter
     * @return  mixed                filtered value
     * @throws  stubFilterException  in case $value has errors
     */
    public function execute($value);
}
?>