<?php
/**
 * Tests for net::stubbles::lang::types::stubDateModifier.
 *
 * @package     stubbles
 * @subpackage  lang_types_test
 * @version     $Id: stubDateModifierTestCase.php 3129 2011-04-01 15:31:01Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::types::stubDateModifier');
/**
 * Tests for net::stubbles::lang::types::stubDateModifier.
 *
 * @package     stubbles
 * @subpackage  lang_types_test
 * @since       1.7.0
 * @group       lang
 * @group       lang_types
 * @group       bug268
 */
class stubDateModifierTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * origin time zone for restoring in tearDown()
     *
     * @var  string
     */
    protected $originTimeZone;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->originTimeZone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->originTimeZone);
    }

    /**
     * helper assertion for the test
     *
     * @param  string    $expected  expected date as string
     * @param  stubDate  $date      date to check for equality to expected date
     */
    protected function assertDateEquals($expected, stubDate $date)
    {
        $this->assertEquals(date_format(date_create($expected), 'U'),
                            date_format($date->getHandle(), 'U'),
                            'Expected ' . $expected . ' but got ' . $date->format('c')
        );
    }

    /**
     * @test
     */
    public function changeDateReturnsNewDateInstance()
    {
        $date        = stubDate::now();
        $changedDate = $date->change()->to('+1 day');
        $this->assertNotSame($date, $changedDate);
        $this->assertTrue($changedDate->isAfter($date));
        $this->assertTrue($changedDate->isAfter(new stubDate('tomorrow')));
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function changeTimeWithInvalidArgumentThrowsIllegalArgumentException()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $date->change()->timeTo('invalid');
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function changeTimeWithInvalidValuesThrowsIllegalArgumentException()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $date->change()->timeTo('in:val:id');
    }

    /**
     * @test
     */
    public function changeTimeReturnsNewInstance()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertNotSame($date,
                             $date->change()->timeTo('14:13:12')
        );
    }

    /**
     * @test
     */
    public function changeTimeChangesTimeOnlyButKeepsDate()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertDateEquals('2011-03-31T14:13:12+00:00',
                                $date->change()->timeTo('14:13:12')
        );
    }

    /**
     * @test
     */
    public function changeHourToOnlyChangesHour()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertDateEquals('2011-03-31T14:00:00+00:00',
                                $date->change()->hourTo(14)
        );
    }

    /**
     * @test
     */
    public function changeByHoursAddsGivenAmountOfHours()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-31T15:01:01+00:00',
                                $date->change()->byHours(14)
        );
    }

    /**
     * @test
     */
    public function changeByHoursChangesDateWhenGivenValueExceedsStandardHours()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-04-01T01:01:01+00:00',
                                $date->change()->byHours(24)
        );
    }

    /**
     * @test
     */
    public function changeByHoursSubtractsNegativeAmountOfHours()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-30T01:01:01+00:00',
                                $date->change()->byHours(-24)
        );
    }

    /**
     * @test
     */
    public function changeMinuteToOnlyChangesMinutes()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertDateEquals('2011-03-31T01:13:00+00:00',
                                $date->change()->minuteTo(13)
        );
    }

    /**
     * @test
     */
    public function changeByMinutesAddsGivenAmountOfMinutes()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-31T01:15:01+00:00',
                                $date->change()->byMinutes(14)
        );
    }

    /**
     * @test
     */
    public function changeByMinutesChangesHoursWhenGivenValueExceedsStandardMinutes()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-31T02:01:01+00:00',
                                $date->change()->byMinutes(60)
        );
    }

    /**
     * @test
     */
    public function changeByMinutesSubtractsNegativeAmountOfMinutes()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-31T00:37:01+00:00',
                                $date->change()->byMinutes(-24)
        );
    }

    /**
     * @test
     */
    public function changeSecondToOnlyChangesSeconds()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertDateEquals('2011-03-31T01:00:12+00:00',
                                $date->change()->secondTo(12)
        );
    }

    /**
     * @test
     */
    public function changeBySecondsAddsGivenAmountOfSeconds()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-31T01:01:15+00:00',
                                $date->change()->bySeconds(14)
        );
    }

    /**
     * @test
     */
    public function changeBySecondsChangesMinutesWhenGivenValueExceedsStandardSeconds()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-31T01:02:01+00:00',
                                $date->change()->bySeconds(60)
        );
    }

    /**
     * @test
     */
    public function changeBySecondsSubtractsNegativeAmountOfSeconds()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-31T01:00:37+00:00',
                                $date->change()->bySeconds(-24)
        );
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function changeDateWithInvalidArgumentThrowsIllegalArgumentException()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $date->change()->dateTo('invalid');
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function changeDateWithInvalidValuesThrowsIllegalArgumentException()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $date->change()->dateTo('in-val-id');
    }

    /**
     * @test
     */
    public function changeDateToReturnsNewInstance()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertNotSame($date,
                             $date->change()->dateTo('2012-7-15')
        );
    }

    /**
     * @test
     */
    public function changeDateToChangesDateOnlyButKeepsTime()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertDateEquals('2012-07-15T01:00:00+00:00',
                                $date->change()->dateTo('2012-7-15')
        );
    }

    /**
     * @test
     */
    public function changeYearToOnlyChangesYear()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertDateEquals('2012-03-31T01:00:00+00:00',
                                $date->change()->yearTo(2012)
        );
    }

    /**
     * @test
     */
    public function changeByYearsAddsGivenAmountOfYears()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2025-03-31T01:01:01+00:00',
                                $date->change()->byYears(14)
        );
    }

    /**
     * @test
     */
    public function changeByYearsSubtractsNegativeAmountOfYears()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2000-03-31T01:01:01+00:00',
                                $date->change()->byYears(-11)
        );
    }

    /**
     * @test
     */
    public function changeMonthToOnlyChangesMonth()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertDateEquals('2011-07-31T01:00:00+00:00',
                                $date->change()->monthTo(7)
        );
    }

    /**
     * @test
     */
    public function changeByMonthsAddsGivenAmountOfMonths()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-07-31T01:01:01+00:00',
                                $date->change()->byMonths(4)
        );
    }

    /**
     * @test
     */
    public function changeByMonthsChangesYearWhenGivenValueExceedsStandardMonths()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2012-03-31T01:01:01+00:00',
                                $date->change()->byMonths(12)
        );
    }

    /**
     * @test
     */
    public function changeByMonthsSubtractsNegativeAmountOfMonths()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2010-05-31T01:01:01+00:00',
                                $date->change()->byMonths(-10)
        );
    }

    /**
     * @test
     */
    public function changeDayToOnlyChangesDay()
    {
        $date = new stubDate('2011-03-31 01:00:00');
        $this->assertDateEquals('2011-03-15T01:00:00+00:00',
                                $date->change()->dayTo(15)
        );
    }

    /**
     * @test
     */
    public function changeByDaysAddsGivenAmountOfDays()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-04-04T01:01:01+00:00',
                                $date->change()->byDays(4)
        );
    }

    /**
     * @test
     */
    public function changeByDaysChangesMonthWhenGivenValueExceedsStandardDays()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-05-10T01:01:01+00:00',
                                $date->change()->byDays(40)
        );
    }

    /**
     * @test
     */
    public function changeByDaysSubtractsNegativeAmountOfDays()
    {
        $date = new stubDate('2011-03-31 01:01:01');
        $this->assertDateEquals('2011-03-26T01:01:01+00:00',
                                $date->change()->byDays(-5)
        );
    }
}
?>
