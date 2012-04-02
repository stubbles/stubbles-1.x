<?php
/**
 * Tests for net::stubbles::util::datespan::stubDateSpanDay.
 *
 * @package     stubbles
 * @subpackage  util_datespan_test
 * @version     $Id: stubDateSpanDayTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanDay');
/**
 * Tests for net::stubbles::util::datespan::stubDateSpanDay.
 *
 * @package     stubbles
 * @subpackage  util_datespan_test
 * @group       util_datespan
 */
class stubDateSpanDayTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test that toString returns a correct representation
     *
     * @test
     */
    public function display()
    {
        $dateSpanDay = new stubDateSpanDay('2007-04-04');
        $this->assertEquals('Wednesday, 04.04.2007', $dateSpanDay->asString());
    }

    /**
     * test that the datespans are returnred correctly
     *
     * @test
     */
    public function getDateSpans()
    {
        $dateSpanDay = new stubDateSpanDay('2007-05-14');
        $dateSpans   = $dateSpanDay->getDateSpans();
        $this->assertEquals(1, count($dateSpans));
        $this->assertSame($dateSpans[0], $dateSpanDay);
    }

    /**
     * test that the datespans detects correctly whether it starts in the future or not
     *
     * @test
     */
    public function isFuture()
    {
        $dateSpanDay = new stubDateSpanDay('tomorrow');
        $this->assertTrue($dateSpanDay->isFuture());
        $dateSpanDay = new stubDateSpanDay('yesterday');
        $this->assertFalse($dateSpanDay->isFuture());
        $dateSpanDay = new stubDateSpanDay('now');
        $this->assertFalse($dateSpanDay->isFuture());
        $dateSpanDay = new stubDateSpanDay();
        $this->assertFalse($dateSpanDay->isFuture());
    }

    /**
     * contains() should return proper boolean results depending on given date
     *
     * @test
     */
    public function containsCalculatesCorrectResult()
    {
        $dateSpanDay = new stubDateSpanDay('2007-04-04');
        $this->assertFalse($dateSpanDay->contains(new stubDate('2007-04-03')));
        $this->assertTrue($dateSpanDay->contains(new stubDate('2007-04-04')));
        $this->assertFalse($dateSpanDay->contains(new stubDate('2007-04-05')));
    }
}
?>