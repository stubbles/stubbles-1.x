<?php
/**
 * Container for a value error list.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubRequestValueErrorCollection.php 2637 2010-08-14 18:25:37Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueError');
/**
 * Container for a value error list.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @since       1.3.0
 */
interface stubRequestValueErrorCollection extends stubObject
{
    /**
     * add a value error to the collection
     *
     * Return value is the added $valueError instance.
     *
     * @param   stubRequestValueError  $valueError
     * @param   string                 $valueName
     * @return  stubRequestValueError
     */
    public function add(stubRequestValueError $valueError, $valueName);

    /**
     * returns number of collected errors
     *
     * @return  int
     */
    public function count();

    /**
     * checks whether there are any errors at all
     *
     * @return  bool
     */
    public function exist();

    /**
     * checks whether a request value has any error
     *
     * @param   string  $valueName  name of request value
     * @return  bool
     */
    public function existFor($valueName);

    /**
     * checks whether a request value has a specific error
     *
     * @param   string  $valueName  name of request value
     * @param   string  $errorId    id of error
     * @return  bool
     */
    public function existForWithId($valueName, $errorId);

    /**
     * returns list of all errors for all request values
     *
     * @return  array<string,array<string,stubRequestValueError>>
     */
    public function get();

    /**
     * returns a list of errors for given request value
     *
     * @param   string                               $valueName
     * @return  array<string,stubRequestValueError>
     */
    public function getFor($valueName);

    /**
     * returns a list of errors for given request value
     *
     * @param   string                 $valueName
     * @param   string                 $errorId    id of error
     * @return  stubRequestValueError
     */
    public function getForWithId($valueName, $errorId);
}
?>