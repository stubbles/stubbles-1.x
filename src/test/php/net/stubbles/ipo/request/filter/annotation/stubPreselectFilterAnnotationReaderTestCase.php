<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubPreselectFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubStringFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubPreselectFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseFilterAnnotationReaderTestCase.php';
/**
 * Helper class to provide allowed values.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 */
class TestPreselectProvider
{
    /**
     * returns allowed values, but needs instance of this class
     *
     * @return  array<string>
     */
    public function getOtherData()
    {
        return array('foo', 'bar');
    }

    /**
     * returns allowed values, but doesn't need instance of this class
     *
     * @return  array<string>
     */
    public static function getData()
    {
        return array('bar', 'baz');
    }
}
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubPreselectFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubPreselectFilterAnnotationReaderTestCase extends stubBaseFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubPreselectFilterAnnotationReader
     */
    protected $preselectFilterAnnotationReader;
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
        $this->mockInjector                    = $this->getMock('stubInjector');
        $this->preselectFilterAnnotationReader = new stubPreselectFilterAnnotationReader($this->mockInjector);
        $refClass                              = new stubReflectionClass('TestPreselectProvider');
        $this->annotation->sourceDataClass     = $refClass;
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->preselectFilterAnnotationReader;
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
     * asserts that created filter is same as original filter
     *
     * @param  stubFilter                    $filter
     * @param  stubFilter|stubFilterBuilder  $createdFilter
     */
    protected function assertOriginalFilter(stubFilter $filter, $createdFilter)
    {
        parent::assertOriginalFilter($filter, $createdFilter->getDecoratedFilter());
    }

    /**
     * @test
     */
    public function providePreselecDataViaStaticProviderMethod()
    {
        $this->annotation->errorId  = 'foo';
        $filter                     = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $filter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $validatorFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubValidatorFilterDecorator',
                                $validatorFilterDecorator
        );
        $this->assertEquals(array('bar', 'baz'),
                            $validatorFilterDecorator->getValidator()
                                                     ->getAllowedValues()
        );
        $this->assertEquals('foo',
                            $validatorFilterDecorator->getErrorId()
        );
        $this->assertSame($filter,
                          $validatorFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function providePreselecDataViaNonStaticProviderMethod()
    {
        $this->annotation->sourceDataMethod = 'getOtherData';
        $filter                             = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $filter);
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('TestPreselectProvider'))
                           ->will($this->returnValue(new TestPreselectProvider()));
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $validatorFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubValidatorFilterDecorator',
                                $validatorFilterDecorator
        );
        $this->assertEquals(array('foo', 'bar'),
                            $validatorFilterDecorator->getValidator()
                                                     ->getAllowedValues()
        );
        $this->assertEquals('FIELD_WRONG_VALUE',
                            $validatorFilterDecorator->getErrorId()
        );
        $this->assertSame($filter,
                          $validatorFilterDecorator->getDecoratedFilter()
        );
    }
}
?>