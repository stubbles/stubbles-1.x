<?php
/**
 * Tests for net::stubbles::util::datespan::stubDateSpanWeek.
 *
 * @package     stubbles
 * @subpackage  util_datespan_test
 * @version     $Id: stubDateSpanWeekTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanWeek');
/**
 * Tests for net::stubbles::util::datespan::stubDateSpanWeek.
 *
 * @package     stubbles
 * @subpackage  util_datespan_test
 * @group       util_datespan
 */
class stubDateSpanWeekTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test that toString returns a correct representation
     *
     * @test
     */
    public function display()
    {
        $dateSpanWeek = new stubDateSpanWeek('2007-04-02');
        $this->assertEquals('14', $dateSpanWeek->asString());
    }

    /**
     * test that the datespans are returnred correctly
     *
     * @test
     */
    public function getDateSpans()
    {
        $dateSpanWeek = new stubDateSpanWeek('2007-05-14');
        $this->assertEquals(7, count($dateSpanWeek->getDateSpans()));
    }

    /**
     * test that the datespans detects correctly whether it starts in the future or not
     *
     * @test
     */
    public function isFuture()
    {
        $dateSpanWeek = new stubDateSpanWeek('tomorrow');
        $this->assertTrue($dateSpanWeek->isFuture());
        $dateSpanWeek = new stubDateSpanWeek('yesterday');
        $this->assertFalse($dateSpanWeek->isFuture());
        $dateSpanWeek = new stubDateSpanWeek('now');
        $this->assertFalse($dateSpanWeek->isFuture());
    }

    /**
     * contains() should return proper boolean results depending on given date
     *
     * @test
     */
    public function containsCalculatesCorrectResult()
    {
        $dateSpanWeek = new stubDateSpanWeek('2009-01-05');
        $this->assertFalse($dateSpanWeek->contains(new stubDate('2009-01-04')));
        $this->assertTrue($dateSpanWeek->contains(new stubDate('2009-01-05')));
        $this->assertTrue($dateSpanWeek->contains(new stubDate('2009-01-06')));
        $this->assertTrue($dateSpanWeek->contains(new stubDate('2009-01-07')));
        $this->assertTrue($dateSpanWeek->contains(new stubDate('2009-01-08')));
        $this->assertTrue($dateSpanWeek->contains(new stubDate('2009-01-09')));
        $this->assertTrue($dateSpanWeek->contains(new stubDate('2009-01-10')));
        $this->assertTrue($dateSpanWeek->contains(new stubDate('2009-01-11')));
        $this->assertFalse($dateSpanWeek->contains(new stubDate('2009-01-12')));
    }
}
?>