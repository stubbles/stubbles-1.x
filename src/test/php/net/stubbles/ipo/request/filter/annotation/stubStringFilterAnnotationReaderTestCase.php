<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubStringFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubStringFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubStringFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseStringFilterAnnotationReaderTestCase.php';
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubStringFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubStringFilterAnnotationReaderTestCase extends stubBaseStringFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubStringFilterAnnotationReader
     */
    protected $stringFilterAnnotationReader;

    /**
     * create test environment
     */
    protected function doSetUp()
    {
        $this->stringFilterAnnotationReader = new stubStringFilterAnnotationReader();
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->stringFilterAnnotationReader;
    }

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected function createFilter()
    {
        return new stubStringFilter();
    }

    /**
     * returns filter type
     *
     * @return  string
     */
    protected function getFilterType()
    {
        return 'string';
    }

    /**
     * @test
     */
    public function withRegularExpression()
    {
        $this->annotation->regex        = '/foo[0-9]{3}/';
        $this->annotation->regexErrorId = 'bar';
        $stringFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $stringFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $validatorFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubValidatorFilterDecorator',
                                $validatorFilterDecorator
        );
        $this->assertEquals('/foo[0-9]{3}/',
                            $validatorFilterDecorator->getValidator()
                                                     ->getValue()
        );
        $this->assertEquals('bar',
                            $validatorFilterDecorator->getErrorId()
        );
        $this->assertSame($stringFilter,
                          $validatorFilterDecorator->getDecoratedFilter()
        );
    }
}
?>