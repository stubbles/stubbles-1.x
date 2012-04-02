<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubPasswordFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubPasswordFilter',
                      'net::stubbles::ipo::request::filter::annotation::stubPasswordFilterAnnotationReader'
);
require_once dirname(__FILE__) . '/stubBaseFilterAnnotationReaderTestCase.php';
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubPasswordFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubPasswordFilterAnnotationReaderTestCase extends stubBaseFilterAnnotationReaderTestCase
{
    /**
     * instance to test
     *
     * @var  stubPasswordFilterAnnotationReader
     */
    protected $passwordFilterAnnotationReader;
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
        $this->mockInjector                   = $this->getMock('stubInjector');
        $this->passwordFilterAnnotationReader = new stubPasswordFilterAnnotationReader($this->mockInjector);
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected function getTestInstance()
    {
        return $this->passwordFilterAnnotationReader;
    }

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected function createFilter()
    {
        return new stubPasswordFilter($this->getMock('stubRequestValueErrorFactory'));
    }

    /**
     * returns filter type
     *
     * @return  string
     */
    protected function getFilterType()
    {
        return 'password';
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
    public function hasMinLength6ByDefault()
    {
        $passwordFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $passwordFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $lengthFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator',
                                $lengthFilterDecorator
        );
        $this->assertEquals(6,
                            $lengthFilterDecorator->getMinLengthValidator()
                                                  ->getValue()
        );
        $this->assertSame($passwordFilter,
                          $lengthFilterDecorator->getDecoratedFilter()
        );
        $this->assertEquals(5,
                            $passwordFilter->getMinDiffChars()
        );
        $this->assertEquals(array(),
                            $passwordFilter->getNonAllowedValues()
        );
    }

    /**
     * @test
     */
    public function hasConfiguredMinLength6()
    {
        $this->annotation->minLength = 8;
        $passwordFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $passwordFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $lengthFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator',
                                $lengthFilterDecorator
        );
        $this->assertEquals(8,
                            $lengthFilterDecorator->getMinLengthValidator()
                                                  ->getValue()
        );
        $this->assertSame($passwordFilter,
                          $lengthFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function withMinDiffChars()
    {
        $this->annotation->minDiffChars = 8;
        $passwordFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $passwordFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $lengthFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator',
                                $lengthFilterDecorator
        );
        $this->assertSame($passwordFilter,
                          $lengthFilterDecorator->getDecoratedFilter()
        );
        $this->assertEquals(8,
                            $passwordFilter->getMinDiffChars()
        );
    }

    /**
     * @test
     */
    public function withEncoderClass()
    {
        $encoderMockClassName = get_class($this->getMock('stubStringEncoder'));
        $this->annotation->encoderClass = new stubReflectionClass($encoderMockClassName);
        $passwordFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $passwordFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $encodingFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubEncodingFilterDecorator',
                                $encodingFilterDecorator
        );
        $this->assertEquals($encoderMockClassName,
                            get_class($encodingFilterDecorator->getEncoder())
        );
        $lengthFilterDecorator = $encodingFilterDecorator->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator',
                                $lengthFilterDecorator
        );
        $this->assertSame($passwordFilter,
                          $lengthFilterDecorator->getDecoratedFilter()
        );
    }
}
?>