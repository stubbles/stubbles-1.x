<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubStringFilterAnnotationReader.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
require_once dirname(__FILE__) . '/stubBaseFilterAnnotationReaderTestCase.php';
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
abstract class stubBaseStringFilterAnnotationReaderTestCase extends stubBaseFilterAnnotationReaderTestCase
{
    /**
     * @test
     */
    public function withMinLength()
    {
        $this->annotation->minLength        = 10;
        $this->annotation->minLengthErrorId = 'foo';
        $stringBasedFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $stringBasedFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $lengthFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator',
                                $lengthFilterDecorator
        );
        $this->assertEquals(10,
                            $lengthFilterDecorator->getMinLengthValidator()
                                                  ->getValue()
        );
        $this->assertEquals('foo',
                            $lengthFilterDecorator->getMinLengthErrorId()
        );
        $this->assertNull($lengthFilterDecorator->getMaxLengthValidator());
        $this->assertSame($stringBasedFilter,
                          $lengthFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function withMaxLength()
    {
        $this->annotation->maxLength        = 10;
        $this->annotation->maxLengthErrorId = 'foo';
        $stringBasedFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $stringBasedFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $lengthFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator',
                                $lengthFilterDecorator
        );
        $this->assertNull($lengthFilterDecorator->getMinLengthValidator());
        $this->assertEquals(10,
                            $lengthFilterDecorator->getMaxLengthValidator()
                                                  ->getValue()
        );
        $this->assertEquals('foo',
                            $lengthFilterDecorator->getMaxLengthErrorId()
        );
        $this->assertSame($stringBasedFilter,
                          $lengthFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function withMinAndMaxLength()
    {
        $this->annotation->minLength = 10;
        $this->annotation->maxLength = 20;
        $stringBasedFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $stringBasedFilter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $lengthFilterDecorator = $createdFilter->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator',
                                $lengthFilterDecorator
        );
        $this->assertEquals(10,
                            $lengthFilterDecorator->getMinLengthValidator()
                                                  ->getValue()
        );
        $this->assertEquals(20,
                            $lengthFilterDecorator->getMaxLengthValidator()
                                                  ->getValue()
        );
        $this->assertSame($stringBasedFilter,
                          $lengthFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function withEncoderClass()
    {
        $encoderMockClassName = get_class($this->getMock('stubStringEncoder'));
        $this->annotation->encoderClass = new stubReflectionClass($encoderMockClassName);
        $stringBasedFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $stringBasedFilter);
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
        $this->assertEquals(stubStringEncoder::MODE_ENCODE,
                            $encodingFilterDecorator->getEncoderMode()
        );
        $this->assertSame($stringBasedFilter,
                          $encodingFilterDecorator->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function withDecoderClass()
    {
        $encoderMockClassName = get_class($this->getMock('stubStringEncoder'));
        $this->annotation->decoderClass = new stubReflectionClass($encoderMockClassName);
        $stringBasedFilter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $stringBasedFilter);
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
        $this->assertEquals(stubStringEncoder::MODE_DECODE,
                            $encodingFilterDecorator->getEncoderMode()
        );
        $this->assertSame($stringBasedFilter,
                          $encodingFilterDecorator->getDecoratedFilter()
        );
    }
}
?>