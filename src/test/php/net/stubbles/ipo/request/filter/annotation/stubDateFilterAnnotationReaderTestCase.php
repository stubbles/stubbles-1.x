<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubDateFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubDateFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubDateFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseFilterAnnotationReaderTestCase.php';
/**
 * Helper class to provide minimum and maximum date for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 */
class TestDateProvider
{
    /**
     * returns minimum date, but needs instance of this class
     *
     * @return  stubDate
     */
    public function getMinDate()
    {
        return new stubDate('2011-01-02');
    }

    /**
     * returns maximum date, but doesn't need instance of this class
     *
     * @return  stubDate
     */
    public static function getMaxDate()
    {
        return new stubDate('2011-01-16');
    }
}
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubDateFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubDateFilterAnnotationReaderTestCase extends stubBaseFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubDateFilterAnnotationReader
     */
    protected $dateFilterAnnotationReader;
    /**
     * mocked injector instance to create min and max date provider
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * create test environment
     */
    protected function doSetUp()
    {
        $this->mockInjector               = $this->getMock('stubInjector');
        $this->dateFilterAnnotationReader = new stubDateFilterAnnotationReader($this->mockInjector);
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->dateFilterAnnotationReader;
    }

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected function createFilter()
    {
        return new stubDateFilter($this->getMock('stubRequestValueErrorFactory'));
    }

    /**
     * returns filter type
     *
     * @return  string
     */
    protected function getFilterType()
    {
        return 'date';
    }

    /**
     * @test
     */
    public function provideMinValueOnly()
    {
        $this->annotation->minDate        = '2011-01-01';
        $this->annotation->minDateErrorId = 'foo';
        $dateFilter                       = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $dateFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $periodFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubPeriodFilterDecorator',
                                $periodFilterDecorator
        );
        $this->assertEquals('2011-01-01',
                            $periodFilterDecorator->getMinDate()
                                                  ->format('Y-m-d')
        );
        $this->assertNull($periodFilterDecorator->getMaxDate());
        $this->assertEquals('foo',
                            $periodFilterDecorator->getMinDateErrorId()
        );
        $this->assertSame($dateFilter,
                          $periodFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function provideMaxValueOnly()
    {
        $this->annotation->maxDate        = '2011-01-17';
        $this->annotation->maxDateErrorId = 'bar';
        $dateFilter                       = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $dateFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $periodFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubPeriodFilterDecorator',
                                $periodFilterDecorator
        );
        $this->assertNull($periodFilterDecorator->getMinDate());
        $this->assertEquals('2011-01-17',
                            $periodFilterDecorator->getMaxDate()
                                                  ->format('Y-m-d')
        );
        $this->assertEquals('bar',
                            $periodFilterDecorator->getMaxDateErrorId()
        );
        $this->assertSame($dateFilter,
                          $periodFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function provideMinAndMaxDateViaAnnotationValues()
    {
        $this->annotation->minDate        = '2011-01-01';
        $this->annotation->maxDate        = '2011-01-17';
        $this->annotation->minDateErrorId = 'foo';
        $this->annotation->maxDateErrorId = 'bar';
        $dateFilter                       = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $dateFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $periodFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubPeriodFilterDecorator',
                                $periodFilterDecorator
        );
        $this->assertEquals('2011-01-01',
                            $periodFilterDecorator->getMinDate()
                                                  ->format('Y-m-d')
        );
        $this->assertEquals('2011-01-17',
                            $periodFilterDecorator->getMaxDate()
                                                  ->format('Y-m-d')
        );
        $this->assertEquals('foo',
                            $periodFilterDecorator->getMinDateErrorId()
        );
        $this->assertEquals('bar',
                            $periodFilterDecorator->getMaxDateErrorId()
        );
        $this->assertSame($dateFilter,
                          $periodFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function provideMinAndMaxDateViaProviderMethod()
    {
        $refClass = new stubReflectionClass('TestDateProvider');
        $this->annotation->minDateProviderClass  = $refClass;
        $this->annotation->minDateProviderMethod = 'getMinDate';
        $this->annotation->maxDateProviderClass  = $refClass;
        $this->annotation->maxDateProviderMethod = 'getMaxDate';
        $this->annotation->minDateErrorId        = 'foo';
        $this->annotation->maxDateErrorId        = 'bar';
        $dateFilter                       = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $dateFilter);
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('TestDateProvider'))
                           ->will($this->returnValue(new TestDateProvider()));
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $periodFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubPeriodFilterDecorator',
                                $periodFilterDecorator
        );
        $this->assertEquals('2011-01-02',
                            $periodFilterDecorator->getMinDate()
                                                  ->format('Y-m-d')
        );
        $this->assertEquals('2011-01-16',
                            $periodFilterDecorator->getMaxDate()
                                                  ->format('Y-m-d')
        );
        $this->assertEquals('foo',
                            $periodFilterDecorator->getMinDateErrorId()
        );
        $this->assertEquals('bar',
                            $periodFilterDecorator->getMaxDateErrorId()
        );
        $this->assertSame($dateFilter,
                          $periodFilterDecorator->getDecoratedFilter()
        );
    }
}
?>