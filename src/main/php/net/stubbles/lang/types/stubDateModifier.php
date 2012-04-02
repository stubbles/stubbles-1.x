<?php
/**
 * Class for date/time modifications.
 *
 * @package     stubbles
 * @subpackage  lang_types
 * @version     $Id: stubDateModifier.php 3129 2011-04-01 15:31:01Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::types::stubDate'
);
/**
 * Class for date/time modifications.
 *
 * @package     stubbles
 * @subpackage  lang_types
 * @since       1.7.0
 */
class stubDateModifier extends stubBaseObject
{
    /**
     * original date to base modifications on
     *
     * @var  stubDate
     */
    protected $originalDate;

    /**
     * constructor
     *
     * @param  stubDate  $originalDate
     */
    public function __construct(stubDate $originalDate)
    {
        $this->originalDate = $originalDate;
    }

    /**
     * returns a new date instance which represents the changed date
     *
     * @param   string    $target  relative format accepted by strtotime()
     * @return  stubDate
     */
    public function to($target)
    {
        $modifiedHandle = clone $this->originalDate->getHandle();
        $modifiedHandle->modify($target);
        return new stubDate($modifiedHandle);
    }

    /**
     * returns a new date instance with same date but changed time
     *
     * @param   string    $time  time representation in format HH:MM:SS
     * @return  stubDate
     * @throws  stubIllegalArgumentException
     */
    public function timeTo($time)
    {
        $times = explode(':', $time);
        if (count($times) != 3) {
            throw new stubIllegalArgumentException('Given time ' . $time . ' does not follow required format HH:MM:SS');
        }

        list($hour, $minute, $second) = $times;
        return $this->createDateWithNewTime($hour, $minute, $second);
    }

    /**
     * returns a new date instance with same date, minute and second but changed hour
     *
     * @param   int       $hour
     * @return  stubDate
     */
    public function hourTo($hour)
    {
        return $this->createDateWithNewTime($hour, $this->originalDate->getMinutes(), $this->originalDate->getSeconds());
    }

    /**
     * changes date by given amount of hours
     *
     * @param   int       $hours
     * @return  stubDate
     */
    public function byHours($hours)
    {
        return $this->hourTo($this->originalDate->getHours() + $hours);
    }

    /**
     * returns a new date instance with same date, hour and second but changed minute
     *
     * @param   int       $minute
     * @return  stubDate
     */
    public function minuteTo($minute)
    {
        return $this->createDateWithNewTime($this->originalDate->getHours(), $minute, $this->originalDate->getSeconds());
    }

    /**
     * changes date by given amount of minutes
     *
     * @param   int       $minutes
     * @return  stubDate
     */
    public function byMinutes($minutes)
    {
        return $this->minuteTo($this->originalDate->getMinutes() + $minutes);
    }

    /**
     * returns a new date instance with same date, hour and minute but changed second
     *
     * @param   int       $second
     * @return  stubDate
     */
    public function secondTo($second)
    {
        return $this->createDateWithNewTime($this->originalDate->getHours(), $this->originalDate->getMinutes(), $second);
    }

    /**
     * changes date by given amount of seconds
     *
     * @param   int       $seconds
     * @return  stubDate
     */
    public function bySeconds($seconds)
    {
        return $this->secondTo($this->originalDate->getSeconds() + $seconds);
    }

    /**
     * creates new date instance with changed time
     *
     * @param   int       $hour
     * @param   int       $minute
     * @param   int       $second
     * @return  stubDate
     * @throws  stubIllegalArgumentException
     */
    protected function createDateWithNewTime($hour, $minute, $second)
    {
        $modifiedHandle = clone $this->originalDate->getHandle();
        if (false === @$modifiedHandle->setTime($hour, $minute, $second)) {
            throw new stubIllegalArgumentException('Given values for hour, minute and/or second not suitable for changing the time.');
        }

        return new stubDate($modifiedHandle);
    }

    /**
     * returns a new date instance with changed date but same time
     *
     * @param   string    $date  date representation in format YYYY-MM-DD
     * @return  stubDate
     * @throws  stubIllegalArgumentException
     */
    public function dateTo($date)
    {
        $dates = explode('-', $date);
        if (count($dates) != 3) {
            throw new stubIllegalArgumentException('Given date ' . $date . ' does not follow required format YYYY-MM-DD');
        }

        list($year, $month, $day) = $dates;
        return $this->createNewDateWithExistingTime($year, $month, $day);
    }

    /**
     * returns a new date instance with changed year but same time, month and day
     *
     * @param   string    $year
     * @return  stubDate
     */
    public function yearTo($year)
    {
        return $this->createNewDateWithExistingTime($year, $this->originalDate->getMonth(), $this->originalDate->getDay());
    }

    /**
     * changes date by given amount of years
     *
     * @param   int       $years
     * @return  stubDate
     */
    public function byYears($years)
    {
        return $this->yearTo($this->originalDate->getYear() + $years);
    }

    /**
     * returns a new date instance with changed month but same time, year and day
     *
     * @param   string    $month
     * @return  stubDate
     */
    public function monthTo($month)
    {
        return $this->createNewDateWithExistingTime($this->originalDate->getYear(), $month, $this->originalDate->getDay());
    }

    /**
     * changes date by given amount of months
     *
     * @param   int       $months
     * @return  stubDate
     */
    public function byMonths($months)
    {
        return $this->monthTo($this->originalDate->getMonth() + $months);
    }

    /**
     * returns a new date instance with changed day but same time, year and month
     *
     * @param   string    $day
     * @return  stubDate
     */
    public function dayTo($day)
    {
        return $this->createNewDateWithExistingTime($this->originalDate->getYear(), $this->originalDate->getMonth(), $day);
    }

    /**
     * changes date by given amount of days
     *
     * @param   int       $days
     * @return  stubDate
     */
    public function byDays($days)
    {
        return $this->dayTo($this->originalDate->getDay() + $days);
    }

    /**
     * creates new date instance with changed date but same time
     *
     * @param   int       $year
     * @param   int       $month
     * @param   int       $day
     * @return  stubDate
     * @throws  stubIllegalArgumentException
     */
    protected function createNewDateWithExistingTime($year, $month, $day)
    {
        $modifiedHandle = clone $this->originalDate->getHandle();
        if (false === @$modifiedHandle->setDate($year, $month, $day)) {
            throw new stubIllegalArgumentException('Given values for year, month and/or day not suitable for changing the date.');
        }

        return new stubDate($modifiedHandle);
    }
}
?>