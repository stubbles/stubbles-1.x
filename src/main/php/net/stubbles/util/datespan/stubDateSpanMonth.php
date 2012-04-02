<?php
/**
 * Datespan that represents a month.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 * @version     $Id: stubDateSpanMonth.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanCustom');
/**
 * Datespan that represents a month.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 */
class stubDateSpanMonth extends stubDateSpanCustom implements stubDateSpan
{
    /**
     * constructor
     *
     * If no value for the year is supplied the current year will be used.
     *
     * If no value for the month is supplied the current month will be used.
     * However, if the current day is the first of a month, the datespan will
     * cover the last month. If today is the first of january, then the
     * datespan will cover the december of last year.
     *
     * @param  int     $year      optional  year of the span
     * @param  int     $month     optional  month of the span
     * @param  string  $interval  optional  interval of the span
     */
    public function __construct($year = null, $month = null, $interval = stubDateSpan::INTERVAL_DAY)
    {
        if (null === $year) {
            $year = (int) date('Y');
        }

        if (null === $month) {
            $month = (int) date('m');
            $day   = (int) date('d');
            // if today is first day of the month use previous month
            if (1 === $day) {
                // if month is January set $month to December of the last year, else decrease month
                if (1 === $month) {
                    $month = 12;
                    $year--;
                } else {
                    $month--;
                }
            }
        }

        $start = new DateTime();
        $start->setDate($year, $month, 1);
        $start->setTime(0, 0, 0);
        $end = new DateTime();
        $end->setDate($year, $month, $start->format('t'));
        $end->setTime(23, 59, 59);
        parent::__construct(new stubDate($start), new stubDate($end), $interval);
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->from->format('Y-m');
    }
}
?>