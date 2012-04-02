<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubPeriodFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubPeriodFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubPeriodFilterDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubPeriodFilterDecorator extends stubPeriodFilterDecorator
{
    /**
     * helper method for direct access to protected doExecute()
     *
     * @param   mixed  $value
     * @return  mixed
     */
    public function callDoExecute($value)
    {
        return $this->doExecute($value);
    }
}
/**
 * Tests for net::stubbles::ipo::request::filter::stubPeriodFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubPeriodFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * a mock to be used for the rveFactory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequestValueErrorFactory;
    /**
     * the instance to test
     *
     * @var  TeststubPeriodFilterDecorator
     */
    protected $periodFilterDecorator;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockRequestValueErrorFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->periodFilterDecorator        = new TeststubPeriodFilterDecorator($this->getMock('stubFilter'), $this->mockRequestValueErrorFactory);
    }

    /**
     * if no date instance is supplied for the check the return value will be null
     *
     * @test
     */
    public function noDateSuppliedForCheckReturnsNull()
    {
        $this->assertNull($this->periodFilterDecorator->callDoExecute(null));
        $this->assertNull($this->periodFilterDecorator->callDoExecute(''));
        $this->assertNull($this->periodFilterDecorator->callDoExecute(new stdClass()));
    }

    /**
     * setting no min or max date will just pass the value
     *
     * @test
     */
    public function noMinOrMaxDateSet()
    {
        $date = stubDate::now();
        $this->assertSame($date, $this->periodFilterDecorator->callDoExecute($date));
        $this->assertNull($this->periodFilterDecorator->getMinDate());
        $this->assertNull($this->periodFilterDecorator->getMaxDate());
    }

    /**
     * a date after the mindate should be returned as is
     *
     * @test
     */
    public function minDatePassReturnsDate()
    {
        $minDate   = new stubDate('2008-09-26');
        $testDate1 = new stubDate('2008-09-26');
        $testDate2 = new stubDate('2008-09-27');
        $this->periodFilterDecorator->setMinDate($minDate);
        $this->assertSame($minDate, $this->periodFilterDecorator->getMinDate());
        $this->assertNull($this->periodFilterDecorator->getMaxDate());
        $this->assertSame($testDate1, $this->periodFilterDecorator->callDoExecute($testDate1));
        $this->assertSame($testDate2, $this->periodFilterDecorator->callDoExecute($testDate2));
    }

    /**
     * a date before mindate throws a filter exception
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function minDateFailsThrowsFilterException()
    {
        $minDate  = new stubDate('2008-09-26');
        $testDate = new stubDate('2008-09-25');
        $this->periodFilterDecorator->setMinDate($minDate);
        $this->assertEquals('DATE_TOO_EARLY', $this->periodFilterDecorator->getMinDateErrorId());
        $this->assertSame($minDate, $this->periodFilterDecorator->getMinDate());
        $this->assertNull($this->periodFilterDecorator->getMaxDate());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('DATE_TOO_EARLY'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->periodFilterDecorator->callDoExecute($testDate);
    }

    /**
     * a date before mindate throws a filter exception
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function minDateFailsThrowsFilterExceptionWithDifferentErrorId()
    {
        $minDate  = new stubDate('2008-09-26');
        $testDate = new stubDate('2008-09-25');
        $this->periodFilterDecorator->setMinDate($minDate, 'differentErrorId');
        $this->assertEquals('differentErrorId', $this->periodFilterDecorator->getMinDateErrorId());
        $this->assertSame($minDate, $this->periodFilterDecorator->getMinDate());
        $this->assertNull($this->periodFilterDecorator->getMaxDate());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('differentErrorId'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->periodFilterDecorator->callDoExecute($testDate);
    }

    /**
     * a date before the maxdate should be returned as is
     *
     * @test
     */
    public function maxLength()
    {
        $maxDate   = new stubDate('2008-09-26');
        $testDate1 = new stubDate('2008-09-26');
        $testDate2 = new stubDate('2008-09-25');
        $this->periodFilterDecorator->setMaxDate($maxDate);
        $this->assertNull($this->periodFilterDecorator->getMinDate());
        $this->assertSame($maxDate, $this->periodFilterDecorator->getMaxDate());
        $this->assertSame($testDate1, $this->periodFilterDecorator->callDoExecute($testDate1));
        $this->assertSame($testDate2, $this->periodFilterDecorator->callDoExecute($testDate2));
    }

    /**
     * a date after maxdate throws a filter exception
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function maxDateFailsThrowsFilterException()
    {
        $maxDate  = new stubDate('2008-09-26');
        $testDate = new stubDate('2008-09-27');
        $this->periodFilterDecorator->setMaxDate($maxDate);
        $this->assertNull($this->periodFilterDecorator->getMinDate());
        $this->assertSame($maxDate, $this->periodFilterDecorator->getMaxDate());
        $this->assertEquals('DATE_TOO_LATE', $this->periodFilterDecorator->getMaxDateErrorId());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('DATE_TOO_LATE'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->periodFilterDecorator->callDoExecute($testDate);
    }

    /**
     * a date after maxdate throws a filter exception
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function maxDateFailsThrowsFilterExceptionWithDifferentErrorId()
    {
        $maxDate  = new stubDate('2008-09-26');
        $testDate = new stubDate('2008-09-27');
        $this->periodFilterDecorator->setMaxDate($maxDate, 'differentErrorId');
        $this->assertEquals('differentErrorId', $this->periodFilterDecorator->getMaxDateErrorId());
        $this->assertNull($this->periodFilterDecorator->getMinDate());
        $this->assertSame($maxDate, $this->periodFilterDecorator->getMaxDate());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('differentErrorId'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->periodFilterDecorator->callDoExecute($testDate);
    }
}
?>