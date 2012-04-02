<?php
/**
 * Mock factory to create mock filters, to be used in unit tests.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_mock
 * @version     $Id: stubMockFilterFactory.php 2329 2009-09-16 16:00:05Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilterFactory',
                      'net::stubbles::ipo::request::filter::mock::stubMockFilter'
);
/**
 * Mock factory to create mock filters, to be used in unit tests.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_mock
 */
class stubMockFilterFactory extends stubBaseObject implements stubFilterFactory
{
    /**
     * creates a filter for the given type
     *
     * @param   string             $type  type of filter to create
     * @return  stubFilterBuilder
     */
    public function createForType($type)
    {
        return new stubMockFilter();
    }

    /**
     * create a builder instance for an existing filter
     *
     * @param   stubFilter         $filter
     * @return  stubFilterBuilder
     */
    public function createBuilder(stubFilter $filter)
    {
        return new stubMockFilter();
    }
}
?>