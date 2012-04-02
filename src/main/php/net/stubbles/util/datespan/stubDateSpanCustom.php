<?php
/**
 * Datespan with a custom start and end date.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 * @version     $Id: stubDateSpanCustom.php 3230 2011-11-23 17:04:19Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpan');
/**
 * Datespan with a custom start and end date.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 */
class stubDateSpanCustom extends stubSerializableObject implements stubDateSpan
{
    /**
     * start date of the span
     *
     * @var  stubDate
     */
    protected $from;
    /**
     * end date of the span
     *
     * @var  stubDate
     */
    protected $to;
    /**
     * The interval of the span (e.g. day, week, month)
     *
     * @var  string
     * @see  DateSpan::INTERVAL_*
     */
    protected $interval;

    /**
     * constructor
     *
     * @param  string|stubDate  $from      start date of the span
     * @param  string|stubDate  $to        end date of the span
     * @param  string           $interval  optional  interval of the span
     */
    public function __construct($from, $to, $interval = stubDateSpan::INTERVAL_DAY)
    {
        if (($from instanceof stubDate) === false) {
            $from = new stubDate($from);
        }

        if (($to instanceof stubDate) === false) {
            $to = new stubDate($to);
        }

        $this->from     = $from;
        $this->to       = $to;
        $this->interval = $interval;
    }

    /**
     * returns the start date
     *
     * @return  stubDate
     */
    public function getStartDate()
    {
        return $this->from;
    }

    /**
     * returns the end date
     *
     * @return  stubDate
     */
    public function getEndDate()
    {
        return $this->to;
    }

    /**
     * returns the spans between the start date and the end date
     *
     * @return  array<stubDateSpan>
     */
    public function getDateSpans()
    {
        $spans = array();
        switch ($this->interval) {
            case stubDateSpan::INTERVAL_DAY:
                stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanDay');
                $day   = $this->from;
                $end   = $this->to->format('U');
                while ($day->format('U') <= $end) {
                    $spans[] = new stubDateSpanDay(clone $day);
                    $day = $day->change()->to('+1 day');
                }
                break;

            case stubDateSpan::INTERVAL_WEEK:
                stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanWeek');
                $day   = $this->from;
                $end   = $this->to->format('U');
                while ($day->format('U') <= $end) {
                    $spans[] = new stubDateSpanWeek(clone $day);
                    $day = $day->change()->to('+7 days');
                }
                break;

            case stubDateSpan::INTERVAL_MONTH:
                stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanMonth');
                $day = $this->from;
                $end = $this->to->format('U');
                while ($day->format('U') <= $end) {
                    $spans[] = new stubDateSpanMonth($day->format('Y'), $day->format('m'));
                    $day = $day->change()->to('+1 month');
                }
                break;

            default:
                // intentionally empty
        }

        return $spans;
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->from->format('d.m.Y') . ' - ' . $this->to->format('d.m.Y');
    }

    /**
     * checks whether the DateSpan is in the future
     *
     * @return  bool
     */
    public function isFuture()
    {
        $today = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
        if ($this->from->format('U') > $today) {
            return true;
        }

        return false;
    }

    /**
     * checks whether the span contains the given date
     *
     * @param   stubDate  $date
     * @return  bool
     */
    public function contains(stubDate $date)
    {
        if ($this->from->isBefore($date) === false && $this->from->equals($date) === false) {
            return false;
        }

        if ($this->to->isAfter($date) === false && $this->to->equals($date) === false) {
            return false;
        }

        return true;
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
        if ('from' == $name || 'to' == $name) {
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
        if ('from' == $name || 'to' == $name) {
            $this->$name = new stubDate($serializedValue);
            return;
        }

        parent::__doUnserialize($name, $serializedValue);
    }
}
?>