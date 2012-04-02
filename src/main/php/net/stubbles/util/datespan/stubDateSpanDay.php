<?php
/**
 * Datespan that represents a single day.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 * @version     $Id: stubDateSpanDay.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanCustom');
/**
 * Datespan that represents a single day.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 */
class stubDateSpanDay extends stubDateSpanCustom implements stubDateSpan
{
    /**
     * constructor
     *
     * @param  string|stubDate  $day  optional  day that the span covers
     */
    public function __construct($day = null)
    {
        if (null === $day) {
            $day = stubDate::now();
        } elseif ('yesterday' === $day) {
            $day = new stubDate($day);
        }

        parent::__construct($day, $day);
    }

    /**
     * returns the spans between the start date and the end date
     *
     * @return  array<stubDateSpan>
     */
    public function getDateSpans()
    {
        return array($this);
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->from->format('l, d.m.Y');
    }
}
?>