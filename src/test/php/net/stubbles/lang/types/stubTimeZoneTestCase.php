<?php
/**
 * Tests for net::stubbles::lang::types::stubTimeZone.
 *
 * @package     stubbles
 * @subpackage  lang_types_test
 * @version     $Id: stubTimeZoneTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::types::stubTimeZone');
/**
 * Tests for net::stubbles::lang::types::stubTimeZone.
 *
 * @package     stubbles
 * @subpackage  lang_types_test
 * @group       lang
 * @group       lang_types
 */
class stubTimeZoneTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubTimeZone
     */
    protected $timeZone;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->timeZone = new stubTimeZone('Europe/Berlin');
    }

    /**
     * name of the time zone should be returned
     *
     * @test
     */
    public function name()
    {
        $this->assertEquals('Europe/Berlin', $this->timeZone->getName());
    }

    /**
     * offset in summer time should be 2 hours
     *
     * @test
     */
    public function offsetDST()
    {
        $this->assertEquals('+0200', $this->timeZone->getOffset(new stubDate('2007-08-21')));
        $this->assertEquals(7200, $this->timeZone->getOffsetInSeconds(new stubDate('2007-08-21')));
    }

    /**
     * offset in non-summer time should be 1 hour
     *
     * @test
     */
    public function offsetNoDST()
    {
        $this->assertEquals('+0100', $this->timeZone->getOffset(new stubDate('2007-01-21')));
        $this->assertEquals(3600, $this->timeZone->getOffsetInSeconds(new stubDate('2007-01-21')));
    }

    /**
     * offset in seconds for current date is 3600 seconds or 7200 seconds, depending
     * whether we are in dst or not
     *
     * @test
     */
    public function offsetForCurrentDateIs3600SecondsOr7200SecondsDependingWhetherInDstOrNot()
    {
        $offset = $this->timeZone->getOffsetInSeconds();
        $this->assertTrue((3600 === $offset || 7200 === $offset));
    }

    /**
     * offset for some time zones is just an half hour more
     *
     * @test
     */
    public function offsetWithHalfHourDST()
    {
        $timeZone = new stubTimeZone('Australia/Adelaide');
        $this->assertEquals('+1030', $timeZone->getOffset(new stubDate('2007-01-21')));
    }

    /**
     * offset for some time zones is just an half hour more
     *
     * @test
     */
    public function offsetWithHalfHourNoDST()
    {
        $timeZone = new stubTimeZone('Australia/Adelaide');
        $this->assertEquals('+0930', $timeZone->getOffset(new stubDate('2007-08-21')));
    }

    /**
     * a date should be translatable into a date of our current time zone
     *
     * @test
     */
    public function translate()
    {
        $date = new stubDate('2007-01-01 00:00 Australia/Sydney');
        $this->assertEquals(new stubDate('2006-12-31 14:00:00 Europe/Berlin'), $this->timeZone->translate($date));
    }

    /**
     * a time zone with dst should be marked as such
     *
     * @test
     */
    public function timeZonesHavingDstShouldBeMarkedAsSuch()
    {
        $this->assertTrue($this->timeZone->hasDst());
    }

    /**
     * timezone instances are equal if they represent the same timezone
     *
     * @test
     */
    public function timeZonesAreEqualsIfTheyRepresentTheSameTimeZoneString()
    {
        $this->assertTrue($this->timeZone->equals($this->timeZone));
        $this->assertTrue($this->timeZone->equals(new stubTimeZone('Europe/Berlin')));
        $this->assertFalse($this->timeZone->equals(new stubTimeZone('Australia/Adelaide')));
        $this->assertFalse($this->timeZone->equals(new stdClass()));
    }

    /**
     * illegal time zone value throws illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidTimeZoneValueThrowsIllegalArgumentExceptionOnConstruction()
    {
        $timeZone = new stubTimeZone(500);
    }

    /**
     * non existing time zone value throws illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function nonExistingTimeZoneValueThrowsIllegalArgumentExceptionOnConstruction()
    {
        $timeZone = new stubTimeZone('Europe/Karlsruhe');
    }

    /**
     * time zone instance can be transformed into a readable representation
     *
     * @test
     */
    public function toStringConversionCreatesReadableRepresentation()
    {
        $this->assertEquals("net::stubbles::lang::types::stubTimeZone {\n    timeZone(string): Europe/Berlin\n}\n", (string) $this->timeZone);
    }
}
?>