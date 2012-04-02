<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubFloatFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFloatFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubFloatFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseFilterAnnotationReaderTestCase.php';
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubFloatFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubFloatFilterAnnotationReaderTestCase extends stubBaseFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubFloatFilterAnnotationReader
     */
    protected $floatFilterAnnotationReader;

    /**
     * create test environment
     */
    protected function doSetUp()
    {
        $this->floatFilterAnnotationReader = new stubFloatFilterAnnotationReader();
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->floatFilterAnnotationReader;
    }

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected function createFilter()
    {
        return new stubFloatFilter();
    }

    /**
     * returns filter type
     *
     * @return  string
     */
    protected function getFilterType()
    {
        return 'float';
    }

    /**
     * @test
     */
    public function provideMinValue()
    {
        $this->annotation->decimals   = 3;
        $this->annotation->minValue   = -1;
        $this->annotation->minErrorId = 'foo';
        $floatFilter                  = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $floatFilter);
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
        $this->assertSame($floatFilter,
                          $rangeFilterDecorator->getDecoratedFilter()
        );
        $this->assertEquals(3, $floatFilter->getDecimals());
    }

    /**
     * @test
     */
    public function provideMaxValue()
    {
        $this->annotation->maxValue   = -1;
        $this->annotation->maxErrorId = 'foo';
        $floatFilter                  = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $floatFilter);
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
        $this->assertSame($floatFilter,
                          $rangeFilterDecorator->getDecoratedFilter()
        );
        $this->assertNull($floatFilter->getDecimals());
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
        $floatFilter                  = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $floatFilter);
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
        $this->assertSame($floatFilter,
                          $rangeFilterDecorator->getDecoratedFilter()
        );
        $this->assertNull($floatFilter->getDecimals());
    }
}
?>