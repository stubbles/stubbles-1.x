<?php
/**
 * Factory to create filters.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubFilterFactory.php 2328 2009-09-16 15:23:11Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter');
/**
 * Factory to create filters.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @ImplementedBy(net::stubbles::ipo::request::filter::stubDefaultFilterFactory.class)
 */
interface stubFilterFactory extends stubObject
{
    /**
     * creates a filter for the given type
     *
     * @param   string             $type  type of filter to create
     * @return  stubFilterBuilder
     */
    public function createForType($type);

    /**
     * create a builder instance for an existing filter
     *
     * @param   stubFilter         $filter
     * @return  stubFilterBuilder
     */
    public function createBuilder(stubFilter $filter);
}
?>