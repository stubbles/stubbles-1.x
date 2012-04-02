<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubHTTPURLFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubHTTPURLFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubHTTPURLFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseFilterAnnotationReaderTestCase.php';
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubHTTPURLFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubHTTPURLFilterAnnotationReaderTestCase extends stubBaseFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubHTTPURLFilterAnnotationReader
     */
    protected $httpUrlFilterAnnotationReader;

    /**
     * create test environment
     */
    protected function doSetUp()
    {
        $this->httpUrlFilterAnnotationReader = new stubHTTPURLFilterAnnotationReader();
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->httpUrlFilterAnnotationReader;
    }

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected function createFilter()
    {
        return new stubHTTPURLFilter($this->getMock('stubRequestValueErrorFactory'));
    }

    /**
     * returns filter type
     *
     * @return  string
     */
    protected function getFilterType()
    {
        return 'http';
    }

    /**
     * @test
     */
    public function enabledDnsCheck()
    {
        $this->annotation->checkDNS = true;
        $filter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $filter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $this->assertSame($filter, $createdFilter->getDecoratedFilter());
        $this->assertTrue($createdFilter->getDecoratedFilter()->isDNSCheckEnabled());
    }
}
?>