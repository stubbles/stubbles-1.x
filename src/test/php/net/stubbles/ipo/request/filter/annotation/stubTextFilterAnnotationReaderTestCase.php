<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubTextFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubTextFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubTextFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseStringFilterAnnotationReaderTestCase.php';
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubTextFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubTextFilterAnnotationReaderTestCase extends stubBaseStringFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubTextFilterAnnotationReader
     */
    protected $textFilterAnnotationReader;

    /**
     * create test environment
     */
    protected function doSetUp()
    {
        $this->textFilterAnnotationReader = new stubTextFilterAnnotationReader();
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->textFilterAnnotationReader;
    }

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected function createFilter()
    {
        return new stubTextFilter();
    }

    /**
     * returns filter type
     *
     * @return  string
     */
    protected function getFilterType()
    {
        return 'text';
    }

    /**
     * @test
     */
    public function withAllowedTags()
    {
        $this->annotation->allowedTags = 'a, strong, span';
        $textFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $textFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $this->assertSame($textFilter,
                          $createdFilter->getDecoratedFilter()
        );
        $this->assertEquals(array('a', 'strong', 'span'),
                            $createdFilter->getDecoratedFilter()
                                          ->getAllowedTags()
        );
    }
}
?>