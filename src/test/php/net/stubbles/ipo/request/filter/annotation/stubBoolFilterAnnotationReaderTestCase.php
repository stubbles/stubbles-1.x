<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubBoolFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubBoolFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubBoolFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseFilterAnnotationReaderTestCase.php';
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubBoolFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubBoolFilterAnnotationReaderTestCase extends stubBaseFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubBoolFilterAnnotationReader
     */
    protected $boolFilterAnnotationReader;

    /**
     * create test environment
     */
    protected function doSetUp()
    {
        $this->boolFilterAnnotationReader = new stubBoolFilterAnnotationReader();
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->boolFilterAnnotationReader;
    }

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected function createFilter()
    {
        return new stubBoolFilter();
    }

    /**
     * returns filter type
     *
     * @return  string
     */
    protected function getFilterType()
    {
        return 'bool';
    }
}
?>