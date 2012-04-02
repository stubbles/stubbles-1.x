<?php
/**
 * Tests for net::stubbles::util::datespan::stubDateSpanMonth.
 *
 * @package     stubbles
 * @subpackage  util_datespan_test
 * @version     $Id: stubDateSpanMonthTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanMonth');
/**
 * Tests for net::stubbles::util::datespan::stubDateSpanMonth.
 *
 * @package     stubbles
 * @subpackage  util_datespan_test
 * @group       util_datespan
 */
class stubDateSpanMonthTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test that toString returns a correct representation
     *
     * @test
     */
    public function display()
    {
        $dateSpanMonth = new stubDateSpanMonth(2007, 4);
        $this->assertEquals('2007-04', $dateSpanMonth->asString());
        $dateSpanMonth = new stubDateSpanMonth(null, 4);
        $this->assertEquals(date('Y') . '-04', $dateSpanMonth->asString());
    }

    /**
     * test that the datespans are returnred correctly
     *
     * @test
     */
    public function getDateSpans()
    {
        $dateSpanMonth = new stubDateSpanMonth(2007, 4);
        $this->assertEquals(30, count($dateSpanMonth->getDateSpans()));
        $dateSpanMonth = new stubDateSpanMonth(2007, 3);
        $this->assertEquals(31, count($dateSpanMonth->getDateSpans()));
        $dateSpanMonth = new stubDateSpanMonth(2007, 2);
        $this->assertEquals(28, count($dateSpanMonth->getDateSpans()));
        $dateSpanMonth = new stubDateSpanMonth(2008, 2);
        $this->assertEquals(29, count($dateSpanMonth->getDateSpans()));
    }

    /**
     * test that the datespans detects correctly whether it starts in the future or not
     *
     * @test
     */
    public function isFuture()
    {
        $dateSpanMonth = new stubDateSpanMonth(date('Y') + 1, 7);
        $this->assertTrue($dateSpanMonth->isFuture());
        $dateSpanMonth = new stubDateSpanMonth(date('Y') - 1, 7);
        $this->assertFalse($dateSpanMonth->isFuture());
        $dateSpanMonth = new stubDateSpanMonth(date('Y'), date('m'));
        $this->assertFalse($dateSpanMonth->isFuture());
    }

    /**
     * contains() should return proper boolean results depending on given date
     *
     * @test
     */
    public function containsCalculatesCorrectResult()
    {
        $dateSpanMonth = new stubDateSpanMonth(2007, 4);
        $this->assertFalse($dateSpanMonth->contains(new stubDate('2007-03-31')));
        $this->assertTrue($dateSpanMonth->contains(new stubDate('2007-04-01')));
        $this->assertTrue($dateSpanMonth->contains(new stubDate('2007-04-02')));
        $this->assertTrue($dateSpanMonth->contains(new stubDate('2007-04-15')));
        $this->assertTrue($dateSpanMonth->contains(new stubDate('2007-04-29')));
        $this->assertTrue($dateSpanMonth->contains(new stubDate('2007-04-30')));
        $this->assertFalse($dateSpanMonth->contains(new stubDate('2007-05-01')));
    }
}
?>