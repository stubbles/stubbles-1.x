<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubIntegerFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubIntegerFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubIntegerFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseFilterAnnotationReaderTestCase.php';
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubIntegerFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubIntegerFilterAnnotationReaderTestCase extends stubBaseFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubIntegerFilterAnnotationReader
     */
    protected $integerFilterAnnotationReader;

    /**
     * create test environment
     */
    protected function doSetUp()
    {
        $this->integerFilterAnnotationReader = new stubIntegerFilterAnnotationReader();
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->integerFilterAnnotationReader;
    }

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected function createFilter()
    {
        return new stubIntegerFilter();
    }

    /**
     * returns filter type
     *
     * @return  string
     */
    protected function getFilterType()
    {
        return 'int';
    }

    /**
     * @test
     */
    public function provideMinValue()
    {
        $this->annotation->minValue   = -1;
        $this->annotation->minErrorId = 'foo';
        $integerFilter                = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $integerFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $rangeFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubRangeFilterDecorator',
                                $rangeFilterDecorator
        );
        $this->assertEquals(-1,
                            $rangeFilterDecorator->getMinValidator()
                                                 ->getValue()
        );
        $this->assertNull($rangeFilterDecorator->getMaxValidator());
        $this->assertEquals('foo',
                            $rangeFilterDecorator->getMinErrorId()
        );
        $this->assertSame($integerFilter,
                          $rangeFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function provideMaxValue()
    {
        $this->annotation->maxValue   = -1;
        $this->annotation->maxErrorId = 'foo';
        $integerFilter                = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $integerFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $rangeFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubRangeFilterDecorator',
                                $rangeFilterDecorator
        );
        $this->assertNull($rangeFilterDecorator->getMinValidator());
        $this->assertEquals(-1,
                            $rangeFilterDecorator->getMaxValidator()
                                                 ->getValue()
        );
        $this->assertEquals('foo',
                            $rangeFilterDecorator->getMaxErrorId()
        );
        $this->assertSame($integerFilter,
                          $rangeFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function provideMinAndMaxValue()
    {
        $this->annotation->minValue   = -1;
        $this->annotation->maxValue   = 1;
        $this->annotation->minErrorId = 'foo';
        $this->annotation->maxErrorId = 'bar';
        $integerFilter                = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $integerFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $rangeFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubRangeFilterDecorator',
                                $rangeFilterDecorator
        );
        $this->assertEquals(-1,
                            $rangeFilterDecorator->getMinValidator()
                                                 ->getValue()
        );
        $this->assertEquals(1,
                            $rangeFilterDecorator->getMaxValidator()
                                                 ->getValue()
        );
        $this->assertEquals('foo',
                            $rangeFilterDecorator->getMinErrorId()
        );
        $this->assertEquals('bar',
                            $rangeFilterDecorator->getMaxErrorId()
        );
        $this->assertSame($integerFilter,
                          $rangeFilterDecorator->getDecoratedFilter()
        );
    }
}
?>