<?php
/**
 * Datespan that represents a week.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 * @version     $Id: stubDateSpanWeek.php 3230 2011-11-23 17:04:19Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanCustom');
/**
 * Datespan that represents a week.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 */
class stubDateSpanWeek extends stubDateSpanCustom implements stubDateSpan
{
    /**
     * constructor
     *
     * @param  string|stubDate  $date      start date of the week
     * @param  string           $interval  optional  interval of the span
     */
    public function __construct($date, $interval = stubDateSpan::INTERVAL_DAY)
    {
        if (($date instanceof stubDate) == false) {
            $date = new stubDate($date);
        }

        $end = $date->change()->to('+ 6 days');
        parent::__construct($date, $end, $interval);
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->from->format('W');
    }
}
?>