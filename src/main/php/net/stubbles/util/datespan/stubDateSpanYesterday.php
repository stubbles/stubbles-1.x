<?php
/**
 * Datespan that represents yesterday.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 * @version     $Id: stubDateSpanYesterday.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::datespan::stubDateSpanDay');
/**
 * Datespan that represents yesterday.
 *
 * @package     stubbles
 * @subpackage  util_datespan
 */
class stubDateSpanYesterday extends stubDateSpanDay
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct('yesterday');
    }
}
?>