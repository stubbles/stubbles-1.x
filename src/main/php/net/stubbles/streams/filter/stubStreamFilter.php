<?php
/**
 * Interface for stream filters.
 *
 * @package     stubbles
 * @subpackage  streams_filter
 * @version     $Id: stubStreamFilter.php 2296 2009-08-20 22:17:45Z mikey $
 */
/**
 * Interface for stream filters.
 *
 * @package     stubbles
 * @subpackage  streams_filter
 */
interface stubStreamFilter extends stubObject
{
    /**
     * Decides whether data should be filtered or not.
     *
     * @param   string  $data
     * @return  bool
     */
    public function shouldFilter($data);
}
?>