<?php
/**
 * Interface for the date span classes.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 * @version     $Id: stubDateSpan.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::types::stubDate');
/**
 * Interface for the date span classes.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 */
interface stubDateSpan extends stubObject, stubSerializable
{
    /**
     * datespan interval: day
     */
    const INTERVAL_DAY   = 'day';
    /**
     * datespan interval: week
     */
    const INTERVAL_WEEK  = 'week';
    /**
     * datespan interval: month
     */
    const INTERVAL_MONTH = 'month';

    /**
     * returns the start date
     *
     * @return  stubDate
     */
    public function getStartDate();

    /**
     * returns the end date
     *
     * @return  stubDate
     */
    public function getEndDate();

    /**
     * returns the spans between the start date and the end date
     *
     * @return  array<stubDateSpan>
     */
    public function getDateSpans();

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString();

    /**
     * checks whether the DateSpan is in the future
     *
     * @return  bool
     */
    public function isFuture();

    /**
     * checks whether the span contains the given date
     *
     * @param   stubDate  $date
     * @return  bool
     */
    public function contains(stubDate $date);
}
?>