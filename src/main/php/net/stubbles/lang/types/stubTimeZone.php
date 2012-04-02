<?php
/**
 * Class for time zone handling.
 *
 * @package     stubbles
 * @subpackage  lang_types
 * @version     $Id: stubTimeZone.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::types::stubDate'
);
/**
 * Class for time zone handling.
 *
 * Shameless rip from the XP framework. ;-) Wraps PHP's internal time zone
 * functions for ease of use.
 *
 * @package     stubbles
 * @subpackage  lang_types
 */
class stubTimeZone extends stubBaseObject
{
    /**
     * internal time zone handle
     *
     * @var  DateTimeZone
     */
    protected $timeZone;

    /**
     * constructor
     *
     * Time zone can be a string like 'Europe/Berlin', a DateTimeZone instance
     * or null.
     *
     * @param   string|DateTimeZone   $timeZone  optional
     * @throws  stubIllegalArgumentException
     */
    public function __construct($timeZone = null)
    {
        if (is_string($timeZone) === true) {
            $this->timeZone = @timezone_open($timeZone);
        } elseif (null === $timeZone) {
            $this->timeZone = timezone_open(date_default_timezone_get());
        } else {
            $this->timeZone = $timeZone;
        }
        
        if (($this->timeZone instanceof DateTimeZone) === false) {
            throw new stubIllegalArgumentException('Invalid time zone identifier ' . $timeZone);
        }
    }

    /**
     * returns internal time zone handle
     *
     * @return  DateTimeZone
     */
    public function getHandle()
    {
        return clone $this->timeZone;
    }

    /**
     * returns name of time zone
     *
     * @return  string
     */
    public function getName()
    {
        return $this->timeZone->getName();
    }

    /**
     * returns offset of the time zone
     *
     * @param   stubDate  $date  optional  defaults to current date
     * @return  string
     */
    public function getOffset(stubDate $date = null)
    {
        $offset  = $this->getOffsetInSeconds($date);
        $hours   = intval(abs($offset) / 3600);
        $minutes = (abs($offset)- ($hours * 3600)) / 60;
        return sprintf('%s%02d%02d', ($offset < 0 ? '-' : '+'), $hours, $minutes);
    }

    /**
     * returns offset to given date in seconds
     *
     * Because a timezone may have different offsets when its in DST or non-DST
     * mode, a date object must be given which is used to determine whether DST
     * or non-DST offset should be returned.
     *
     * @param   stubDate  $date  optional  defaults to current date
     * @return  int
     */
    public function getOffsetInSeconds(stubDate $date = null)
    {
        if (null === $date) {
            return $this->timeZone->getOffset(new DateTime('now'));
        }
        
        return $this->timeZone->getOffset($date->getHandle());
    }

    /**
     * checks whether time zone as dst mode or not
     *
     * @return  bool
     */
    public function hasDst()
    {
        // if there is at least one transition the time zone has a dst mode
        return (count($this->timeZone->getTransitions()) > 0);
    }

    /**
     * translates a date from one timezone to a date of this timezone
     *
     * A new date instance will be returned while the given date is not changed.
     *
     * @param   stubDate  $date
     * @return  stubDate
     */
    public function translate(stubDate $date)
    {
        $handle = clone $date->getHandle();
        $handle->setTimezone($this->timeZone);
        return new stubDate($handle);
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($this->getName() === $compare->getName());
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @return  string
     */
    public function __toString()
    {
        return self::getStringRepresentationOf($this, array('timeZone' => $this->timeZone->getName()));
    }
}
?>