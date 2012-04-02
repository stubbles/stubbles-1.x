<?php
/**
 * Class for date/time handling.
 *
 * @package     stubbles
 * @subpackage  lang_types
 * @version     $Id: stubDate.php 3126 2011-03-31 22:39:38Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::types::stubDateModifier',
                      'net::stubbles::lang::types::stubTimeZone'
);
/**
 * Class for date/time handling.
 *
 * Shameless rip from the XP framework. ;-) Wraps PHP's internal date/time
 * functions for ease of use.
 *
 * @package     stubbles
 * @subpackage  lang_types
 * @XMLTag(tagName='date')
 */
class stubDate extends stubSerializableObject
{
    /**
     * internal date/time handle
     *
     * @var  DateTime
     */
    protected $dateTime;

    /**
     * constructor
     *
     * Creates a new date object through either a
     * <ul>
     *   <li>integer - interpreted as timestamp</li>
     *   <li>string - parsed into a date</li>
     *   <li>DateTime object - will be used as is</li>
     *   <li>NULL - creates a date representing the current time</li>
     *  </ul>
     *
     * Timezone assignment works through these rules:
     * <ul>
     *   <li>If the time is given as string and contains a parseable timezone
     *       identifier that one is used.</li>
     *   <li>If no timezone could be determined, the timezone given by the
     *       second parameter is used.</li>
     *   <li>If no timezone has been given as second parameter, the system's
     *       default timezone is used.</li>
     *
     * @param   int|string|DateTime   $dateTime  optional
     * @param   stubTimeZone          $timeZone  optional
     * @throws  stubIllegalArgumentException
     */
    public function __construct($dateTime = null, stubTimeZone $timeZone = null)
    {
        if (is_numeric($dateTime) === true) {
            $this->dateTime = date_create('@' . $dateTime, timezone_open('UTC'));
            if (false !== $this->dateTime) {
                date_timezone_set($this->dateTime, (null === $timeZone) ? (new DateTimeZone(date_default_timezone_get())) : ($timeZone->getHandle()));
            }
        } elseif (is_string($dateTime) === true) {
            try {
                if (null === $timeZone) {
                    $this->dateTime = new DateTime($dateTime);
                } else {
                    $this->dateTime = new DateTime($dateTime, $timeZone->getHandle());
                }
            } catch (Exception $e) {
                throw new stubIllegalArgumentException('Given datetime string ' . $dateTime . ' is not a valid date string.');
            }
        } else {
            $this->dateTime = $dateTime;
        }

        if (($this->dateTime instanceof DateTime) === false) {
            throw new stubIllegalArgumentException('Datetime must be either unix timestamp, well-formed timestamp or instance of DateTime, but was ' . gettype($dateTime) . ' ' . $dateTime);
        }
    }

    /**
     * returns current date/time
     *
     * @param   stubTimeZone  $timeZone  optional
     * @return  stubDate
     */
    public static function now(stubTimeZone $timeZone = null)
    {
        return new self(time(), $timeZone);
    }

    /**
     * returns internal date/time handle
     *
     * @return  DateTime
     * @XMLIgnore
     */
    public function getHandle()
    {
        return clone $this->dateTime;
    }

    /**
     * returns a new date instance which represents the changed date
     *
     * @param   string    $target  relative format accepted by strtotime()
     * @return  stubDate
     * @deprecated  use change()->to($target) instead, will be removed with 1.8.0
     */
    public function changeTo($target)
    {
        return $this->change()->to($target);
    }

    /**
     * returns way to change the date to another
     *
     * @return  stubDateModifier
     * @XMLIgnore
     */
    public function change()
    {
        return new stubDateModifier($this);
    }

    /**
     * returns timestamp for this date/time
     *
     * @return  int
     * @XMLIgnore
     */
    public function getTimestamp()
    {
        return (int) $this->dateTime->format('U');
    }

    /**
     * returns seconds of current date/time
     *
     * @return  int
     * @XMLIgnore
     */
    public function getSeconds()
    {
        return (int) $this->dateTime->format('s');
    }

    /**
     * returns minutes of current date/time
     *
     * @return  int
     * @XMLIgnore
     */
    public function getMinutes()
    {
        return (int) $this->dateTime->format('i');
    }

    /**
     * returns hours of current date/time
     *
     * @return  int
     * @XMLIgnore
     */
    public function getHours()
    {
        return (int) $this->dateTime->format('G');
    }

    /**
     * returns day of current date/time
     *
     * @return  int
     * @XMLIgnore
     */
    public function getDay()
    {
        return (int) $this->dateTime->format('d');
    }

    /**
     * returns month of current date/time
     *
     * @return  int
     * @XMLIgnore
     */
    public function getMonth()
    {
        return (int) $this->dateTime->format('m');
    }

    /**
     * returns year of current date/time
     *
     * @return  int
     * @XMLIgnore
     */
    public function getYear()
    {
        return (int) $this->dateTime->format('Y');
    }

    /**
     * returns offset to UTC in "+MMSS" notation
     *
     * @return  string
     * @XMLIgnore
     */
    public function getOffset()
    {
        return $this->dateTime->format('O');
    }

    /**
     * returns offset to UTC in seconds
     *
     * @return  int
     * @XMLIgnore
     */
    public function getOffsetInSeconds()
    {
        return (int) $this->dateTime->format('Z');
    }

    /**
     * checks whether this date is before a given date
     *
     * @param   stubDate  $date
     * @return  bool
     */
    public function isBefore(self $date)
    {
        return $this->getTimestamp() < $date->getTimestamp();
    }

    /**
     * checks whether this date is after a given date
     *
     * @param   stubDate  $date
     * @return  bool
     */
    public function isAfter(self $date)
    {
        return $this->getTimestamp() > $date->getTimestamp();
    }

    /**
     * returns time zone of this date
     *
     * @return  stubTimeZone
     * @XMLIgnore
     */
    public function getTimeZone()
    {
        return new stubTimeZone($this->dateTime->getTimezone());
    }

    /**
     * returns date as string
     *
     * @return  string
     * @XMLAttribute(attributeName='value')
     */
    public function asString()
    {
        return $this->format('Y-m-d H:i:sO');
    }

    /**
     * returns formatted date/time string
     *
     * @param   string        $format    format, see http://php.net/date
     * @param   stubTimeZone  $timeZone  optional  target time zone of formatted string
     * @return  string
     */
    public function format($format, stubTimeZone $timeZone = null)
    {
        if (null !== $timeZone) {
            return $timeZone->translate($this)->format($format);
        }

        return $this->dateTime->format($format);
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
            return ($this->getTimestamp() === $compare->getTimestamp());
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
        return self::getStringRepresentationOf($this, array('dateTime' => $this->format('Y-m-d H:i:sO')));
    }

    /**
     * make sure handle is cloned as well
     */
    public function __clone()
    {
        $this->dateTime = clone $this->dateTime;
    }

    /**
     * takes care of serializing the value
     *
     * @param  array   &$propertiesToSerialize  list of properties to serialize
     * @param  string  $name                    name of the property to serialize
     * @param  mixed   $value                   value to serialize
     */
    protected function __doSerialize(&$propertiesToSerialize, $name, $value)
    {
        if ('dateTime' == $name) {
            $this->_serializedProperties[$name] = $value->format('c');
            return;
        }

        parent::__doSerialize($propertiesToSerialize, $name, $value);
    }

    /**
     * takes care of unserializing the value
     *
     * @param  string  $name             name of the property
     * @param  mixed   $serializedValue  value of the property
     */
    protected function __doUnserialize($name, $serializedValue)
    {
        if ('dateTime' == $name) {
            $this->dateTime = new DateTime($serializedValue);
            return;
        }

        parent::__doUnserialize($name, $serializedValue);
    }
}
?>