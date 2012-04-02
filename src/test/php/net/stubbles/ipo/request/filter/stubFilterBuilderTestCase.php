<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubFilterBuilder.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubFilterBuilderTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilterBuilder');
/**
 * Tests for net::stubbles::ipo::request::filter::stubFilterFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubFilterBuilderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubFilterBuilder
     */
    protected $filterBuilder;
    /**
     * mocked filter instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockFilter;
    /**
     * mocked rve factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRveFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockFilter     = $this->getMock('stubFilter');
        $this->mockRveFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->filterBuilder  = new stubFilterBuilder($this->mockFilter, $this->mockRveFactory);
    }

    /**
     * @test
     */
    public function rveFactoryHandling()
    {
        $this->assertSame($this->mockRveFactory, $this->filterBuilder->getRveFactory());
        $mockRveFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->using($mockRveFactory));
        $this->assertSame($mockRveFactory, $this->filterBuilder->getRveFactory());
    }

    /**
     * test that range filter is created decorating an integer filter
     *
     * @test
     */
    public function rangeFilter()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inRange(1, 4));
        $rangeFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubRangeFilterDecorator', $rangeFilterDecorator);
        $this->assertEquals(1, $rangeFilterDecorator->getMinValidator()->getValue());
        $this->assertEquals('VALUE_TOO_SMALL', $rangeFilterDecorator->getMinErrorId());
        $this->assertEquals(4, $rangeFilterDecorator->getMaxValidator()->getValue());
        $this->assertEquals('VALUE_TOO_GREAT', $rangeFilterDecorator->getMaxErrorId());
        $this->assertSame($this->mockFilter, $rangeFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that range filter is created decorating an integer filter
     *
     * @test
     */
    public function rangeFilterDifferentErrorIds()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inRange(1, 4, 'differentMin', 'differentMax'));
        $rangeFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubRangeFilterDecorator', $rangeFilterDecorator);
        $this->assertEquals(1, $rangeFilterDecorator->getMinValidator()->getValue());
        $this->assertEquals('differentMin', $rangeFilterDecorator->getMinErrorId());
        $this->assertEquals(4, $rangeFilterDecorator->getMaxValidator()->getValue());
        $this->assertEquals('differentMax', $rangeFilterDecorator->getMaxErrorId());
        $this->assertSame($this->mockFilter, $rangeFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that range filter is created decorating an integer filter
     *
     * @test
     */
    public function rangeFilterWithoutLowerBorder()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inRange(null, 4));
        $rangeFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubRangeFilterDecorator', $rangeFilterDecorator);
        $this->assertNull($rangeFilterDecorator->getMinValidator());
        $this->assertEquals(4, $rangeFilterDecorator->getMaxValidator()->getValue());
        $this->assertSame($this->mockFilter, $rangeFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that range filter is created decorating an integer filter
     *
     * @test
     */
    public function rangeFilterWithoutUpperBorder()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inRange(1, null));
        $rangeFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubRangeFilterDecorator', $rangeFilterDecorator);
        $this->assertEquals(1, $rangeFilterDecorator->getMinValidator()->getValue());
        $this->assertNull($rangeFilterDecorator->getMaxValidator());
        $this->assertSame($this->mockFilter, $rangeFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that integer filter is created
     *
     * @test
     */
    public function rangeFilterWithoutBorder()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inRange(null, null));
        $this->assertSame($this->mockFilter, $this->filterBuilder->getDecoratedFilter());
    }

    /**
     * test that length filter is created decorating a text filter
     *
     * @test
     */
    public function lengthFilter()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->length(2, 5));
        $lengthFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator', $lengthFilterDecorator);
        $this->assertEquals(2, $lengthFilterDecorator->getMinLengthValidator()->getValue());
        $this->assertEquals('STRING_TOO_SHORT', $lengthFilterDecorator->getMinLengthErrorId());
        $this->assertEquals(5, $lengthFilterDecorator->getMaxLengthValidator()->getValue());
        $this->assertEquals('STRING_TOO_LONG', $lengthFilterDecorator->getMaxLengthErrorId());
        $this->assertSame($this->mockFilter, $lengthFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that length filter is created decorating a text filter
     *
     * @test
     */
    public function lengthFilterWithDifferentErrorIds()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->length(2, 5, 'differentMin', 'differentMax'));
        $lengthFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator', $lengthFilterDecorator);
        $this->assertEquals(2, $lengthFilterDecorator->getMinLengthValidator()->getValue());
        $this->assertEquals('differentMin', $lengthFilterDecorator->getMinLengthErrorId());
        $this->assertEquals(5, $lengthFilterDecorator->getMaxLengthValidator()->getValue());
        $this->assertEquals('differentMax', $lengthFilterDecorator->getMaxLengthErrorId());
        $this->assertSame($this->mockFilter, $lengthFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that length filter is created decorating a text filter
     *
     * @test
     */
    public function lengthFilterWithoutLowerBorder()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->length(null, 5));
        $lengthFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator', $lengthFilterDecorator);
        $this->assertNull($lengthFilterDecorator->getMinLengthValidator());
        $this->assertEquals(5, $lengthFilterDecorator->getMaxLengthValidator()->getValue());
        $this->assertSame($this->mockFilter, $lengthFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that length filter is created decorating a text filter
     *
     * @test
     */
    public function lengthFilterWithoutUpperBorder()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->length(2, null));
        $lengthFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubLengthFilterDecorator', $lengthFilterDecorator);
        $this->assertEquals(2, $lengthFilterDecorator->getMinLengthValidator()->getValue());
        $this->assertNull($lengthFilterDecorator->getMaxLengthValidator());
        $this->assertSame($this->mockFilter, $lengthFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that text filter is created
     *
     * @test
     */
    public function lengthFilterWithoutBorder()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->length(null, null));
        $this->assertSame($this->mockFilter, $this->filterBuilder->getDecoratedFilter());
    }

    /**
     * test that required filter is created decorating a string filter
     *
     * @test
     */
    public function requiredFilter()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->asRequired());
        $requiredFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubRequiredFilterDecorator', $requiredFilterDecorator);
        $this->assertEquals('FIELD_EMPTY', $requiredFilterDecorator->getErrorId());
        $this->assertSame($this->mockFilter, $requiredFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that required filter is created decorating a string filter
     *
     * @test
     */
    public function requiredFilterDifferentErrorId()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->asRequired('foo'));
        $requiredFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubRequiredFilterDecorator', $requiredFilterDecorator);
        $this->assertEquals('foo', $requiredFilterDecorator->getErrorId());
        $this->assertSame($this->mockFilter, $requiredFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that default value filter is created decorating a string filter
     *
     * @test
     */
    public function defaultValueFilter()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->defaultsTo('foo'));
        $defaultValueFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubDefaultValueFilterDecorator', $defaultValueFilterDecorator);
        $this->assertEquals('foo', $defaultValueFilterDecorator->getDefaultValue());
        $this->assertSame($this->mockFilter, $defaultValueFilterDecorator->getDecoratedFilter());
    }

    /**
     * @test
     */
    public function defaultValueFilterIsNoOpForNull()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->defaultsTo(null));
        $this->assertSame($this->mockFilter, $this->filterBuilder->getDecoratedFilter());
    }

    /**
     * test that validator filter is created decorating a string filter
     *
     * @test
     */
    public function validatorFilter()
    {
        $mockValidator = $this->getMock('stubValidator');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->validatedBy($mockValidator));
        $validatorFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubValidatorFilterDecorator', $validatorFilterDecorator);
        $this->assertSame($mockValidator, $validatorFilterDecorator->getValidator());
        $this->assertEquals('FIELD_WRONG_VALUE', $validatorFilterDecorator->getErrorId());
        $this->assertSame($this->mockFilter, $validatorFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that validator filter is created decorating a float filter
     *
     * @test
     */
    public function validatorFilterWithErrorId()
    {
        $mockValidator = $this->getMock('stubValidator');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->validatedBy($mockValidator, 'OTHER_ID'));
        $validatorFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubValidatorFilterDecorator', $validatorFilterDecorator);
        $this->assertSame($mockValidator, $validatorFilterDecorator->getValidator());
        $this->assertEquals('OTHER_ID', $validatorFilterDecorator->getErrorId());
        $this->assertSame($this->mockFilter, $validatorFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that validator filter is created decorating a string filter
     *
     * @test
     */
    public function encodingFilter()
    {
        $mockStringEncoder = $this->getMock('stubStringEncoder');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->encodedWith($mockStringEncoder));
        $encodingFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubEncodingFilterDecorator', $encodingFilterDecorator);
        $this->assertSame($mockStringEncoder, $encodingFilterDecorator->getEncoder());
        $this->assertEquals(stubStringEncoder::MODE_ENCODE, $encodingFilterDecorator->getEncoderMode());
        $this->assertSame($this->mockFilter, $encodingFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that validator filter is created decorating a text filter
     *
     * @test
     */
    public function decodingFilter()
    {
        $mockStringEncoder = $this->getMock('stubStringEncoder');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->decodedWith($mockStringEncoder));
        $encodingFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubEncodingFilterDecorator', $encodingFilterDecorator);
        $this->assertSame($mockStringEncoder, $encodingFilterDecorator->getEncoder());
        $this->assertEquals(stubStringEncoder::MODE_DECODE, $encodingFilterDecorator->getEncoderMode());
        $this->assertSame($this->mockFilter, $encodingFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that period filter is created decorating a date filter
     *
     * @test
     */
    public function periodFilter()
    {
        $minDate = new stubDate('2008-09-01');
        $maxDate = new stubDate('2008-09-30');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inPeriod($minDate, $maxDate));
        $periodFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubPeriodFilterDecorator', $periodFilterDecorator);
        $this->assertSame($minDate, $periodFilterDecorator->getMinDate());
        $this->assertEquals('DATE_TOO_EARLY', $periodFilterDecorator->getMinDateErrorId());
        $this->assertSame($maxDate, $periodFilterDecorator->getMaxDate());
        $this->assertEquals('DATE_TOO_LATE', $periodFilterDecorator->getMaxDateErrorId());
        $this->assertEquals('Y-m-d', $periodFilterDecorator->getDateFormat());
        $this->assertSame($this->mockFilter, $periodFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that period filter is created decorating a date filter
     *
     * @test
     */
    public function periodFilterDifferentErrorIds()
    {
        $minDate = new stubDate('2008-09-01');
        $maxDate = new stubDate('2008-09-30');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inPeriod($minDate, $maxDate, 'differentMin', 'differentMax', 'd/m/Y'));
        $periodFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubPeriodFilterDecorator', $periodFilterDecorator);
        $this->assertSame($minDate, $periodFilterDecorator->getMinDate());
        $this->assertEquals('differentMin', $periodFilterDecorator->getMinDateErrorId());
        $this->assertSame($maxDate, $periodFilterDecorator->getMaxDate());
        $this->assertEquals('differentMax', $periodFilterDecorator->getMaxDateErrorId());
        $this->assertEquals('d/m/Y', $periodFilterDecorator->getDateFormat());
        $this->assertSame($this->mockFilter, $periodFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that period filter is created decorating a date filter
     *
     * @test
     */
    public function periodFilterWithoutMinDate()
    {
        $maxDate = new stubDate('2008-09-01');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inPeriod(null, $maxDate));
        $periodFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubPeriodFilterDecorator', $periodFilterDecorator);
        $this->assertNull($periodFilterDecorator->getMinDate());
        $this->assertSame($maxDate, $periodFilterDecorator->getMaxDate());
        $this->assertSame($this->mockFilter, $periodFilterDecorator->getDecoratedFilter());
    }

    /**
     * ttest that period filter is created decorating a date filter
     *
     * @test
     */
    public function periodFilterWithoutMaxDate()
    {
        $minDate = new stubDate('2008-09-01');
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inPeriod($minDate, null));
        $periodFilterDecorator = $this->filterBuilder->getDecoratedFilter();
        $this->assertInstanceOf('stubPeriodFilterDecorator', $periodFilterDecorator);
        $this->assertSame($minDate, $periodFilterDecorator->getMinDate());
        $this->assertNull($periodFilterDecorator->getMaxDate());
        $this->assertSame($this->mockFilter, $periodFilterDecorator->getDecoratedFilter());
    }

    /**
     * test that date filter is created
     *
     * @test
     */
    public function periodFilterWithoutMinAndMaxDateCreatesReturnsDateFilter()
    {
        $this->assertSame($this->filterBuilder, $this->filterBuilder->inPeriod(null, null));
        $this->assertSame($this->mockFilter, $this->filterBuilder->getDecoratedFilter());
    }
}
?>