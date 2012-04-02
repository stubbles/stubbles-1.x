<?php
/**
 * Tests for net::stubbles::util::datespan::stubDateSpanCustom.
 *
 * @package     stubbles
 * @subpackage  util_datespan_test
 * @version     $Id: stubDateSpanCustomTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanCustom');
/**
 * Tests for net::stubbles::util::datespan::stubDateSpanCustom.
 *
 * @package     stubbles
 * @subpackage  util_datespan_test
 * @group       util_datespan
 */
class stubDateSpanCustomTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test that toString returns a correct representation
     *
     * @test
     */
    public function display()
    {
        $dateSpanCustom = new stubDateSpanCustom('2006-04-04', '2006-04-20');
        $this->assertEquals('04.04.2006 - 20.04.2006', $dateSpanCustom->asString());
    }

    /**
     * start date and end date should be returned
     *
     * @test
     */
    public function startAndEndDateAreReturnedFromStringInput()
    {
        $dateSpanCustom = new stubDateSpanCustom('2006-04-04', '2006-04-20');
        $startDate      = $dateSpanCustom->getStartDate();
        $this->assertInstanceOf('stubDate', $startDate);
        $this->assertEquals('2006-04-04', $startDate->format('Y-m-d'));
        $endDate      = $dateSpanCustom->getEndDate();
        $this->assertInstanceOf('stubDate', $endDate);
        $this->assertEquals('2006-04-20', $endDate->format('Y-m-d'));
    }

    /**
     * start date and end date should be returned
     *
     * @test
     */
    public function startAndEndDateAreReturnedFromInstanceInput()
    {
        $startDate      = new stubDate('2006-04-04');
        $endDate        = new stubDate('2006-04-20');
        $dateSpanCustom = new stubDateSpanCustom($startDate, $endDate);
        $this->assertSame($startDate, $dateSpanCustom->getStartDate());
        $this->assertSame($endDate, $dateSpanCustom->getEndDate());
    }

    /**
     * test that the datespans are returned correctly
     *
     * @test
     */
    public function getDateSpansIntervalDay()
    {
        $dateSpanCustom = new stubDateSpanCustom('2007-05-14', '2007-05-27');
        $dateSpans      = $dateSpanCustom->getDateSpans();
        $this->assertEquals(14, count($dateSpans));
        foreach ($dateSpans as $dateSpan) {
            $this->assertInstanceOf('stubDateSpanDay', $dateSpan);
        }
    }

    /**
     * test that the datespans are returned correctly
     *
     * @test
     */
    public function getDateSpansIntervalWeek()
    {
        $dateSpanCustom = new stubDateSpanCustom('2007-05-14', '2007-05-27', stubDateSpan::INTERVAL_WEEK);
        $dateSpans      = $dateSpanCustom->getDateSpans();
        $this->assertEquals(2, count($dateSpans));
        foreach ($dateSpans as $dateSpan) {
            $this->assertInstanceOf('stubDateSpanWeek', $dateSpan);
        }
    }

    /**
     * test that the datespans are returned correctly
     *
     * @test
     */
    public function getDateSpansIntervalMonth()
    {
        $dateSpanCustom = new stubDateSpanCustom('2008-01-01', '2008-12-31', stubDateSpan::INTERVAL_MONTH);
        $dateSpans      = $dateSpanCustom->getDateSpans();
        $this->assertEquals(12, count($dateSpans));
        foreach ($dateSpans as $dateSpan) {
            $this->assertInstanceOf('stubDateSpanMonth', $dateSpan);
        }
    }

    /**
     * test that the datespans detects correctly whether it starts in the future or not
     *
     * @test
     */
    public function isFuture()
    {
        $dateSpanCustom = new stubDateSpanCustom('tomorrow', '+3 days');
        $this->assertTrue($dateSpanCustom->isFuture());
        $dateSpanCustom = new stubDateSpanCustom('yesterday', '+3 days');
        $this->assertFalse($dateSpanCustom->isFuture());
        $dateSpanCustom = new stubDateSpanCustom('-3 days', 'yesterday');
        $this->assertFalse($dateSpanCustom->isFuture());
    }

    /**
     * contains() should return proper boolean results depending on given date
     *
     * @test
     */
    public function containsCalculatesCorrectResult()
    {
        $dateSpanCustom = new stubDateSpanCustom('2006-04-04', '2006-04-20');
        $this->assertFalse($dateSpanCustom->contains(new stubDate('2006-04-03')));
        $this->assertTrue($dateSpanCustom->contains(new stubDate('2006-04-04')));
        $this->assertTrue($dateSpanCustom->contains(new stubDate('2006-04-05')));
        $this->assertTrue($dateSpanCustom->contains(new stubDate('2006-04-10')));
        $this->assertTrue($dateSpanCustom->contains(new stubDate('2006-04-19')));
        $this->assertTrue($dateSpanCustom->contains(new stubDate('2006-04-20')));
        $this->assertFalse($dateSpanCustom->contains(new stubDate('2006-04-21')));
    }

    /**
     * test that serializing and unserializing a datespan works as expected
     *
     * @test
     */
    public function serializing()
    {
        $dateSpanCustom = new stubDateSpanCustom('2007-05-14', '2007-05-27');
        $serialized = serialize($dateSpanCustom);
        $unserialized = unserialize($serialized);
        $this->assertEquals($dateSpanCustom->getStartDate()->format('Y-m-d'), $unserialized->getStartDate()->format('Y-m-d'));
        $this->assertEquals($dateSpanCustom->getEndDate()->format('Y-m-d'), $unserialized->getEndDate()->format('Y-m-d'));
    }
}
?>