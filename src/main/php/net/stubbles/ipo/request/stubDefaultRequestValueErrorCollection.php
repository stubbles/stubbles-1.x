<?php
/**
 * Default implementation of a value error list.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubDefaultRequestValueErrorCollection.php 2637 2010-08-14 18:25:37Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorCollection');
/**
 * Default implementation of a value error list.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @since       1.3.0
 */
class stubDefaultRequestValueErrorCollection extends stubBaseObject implements stubRequestValueErrorCollection
{
    /**
     * list of errors that occurred while applying a filter on a request value
     *
     * @var  array<string,array<string,stubRequestValueError>>
     */
    protected $errors = array();

    /**
     * add a value error to the collection
     *
     * Return value is the added $valueError instance.
     *
     * @param   stubRequestValueError  $valueError
     * @param   string                 $valueName
     * @return  stubRequestValueError
     */
    public function add(stubRequestValueError $valueError, $valueName)
    {
        if (isset($this->errors[$valueName]) === false) {
            $this->errors[$valueName] = array($valueError->getId() => $valueError);
        } else {
            $this->errors[$valueName][$valueError->getId()] = $valueError;
        }

        return $valueError;
    }

    /**
     * returns number of collected errors
     *
     * @return  int
     */
    public function count()
    {
        return count($this->errors);
    }

    /**
     * checks whether there are any errors at all
     *
     * @return  bool
     */
    public function exist()
    {
        return ($this->count() > 0);
    }

    /**
     * checks whether a request value has any error
     *
     * @param   string  $valueName  name of request value
     * @return  bool
     */
    public function existFor($valueName)
    {
        return isset($this->errors[$valueName]);
    }

    /**
     * checks whether a request value has a specific error
     *
     * @param   string  $valueName  name of request value
     * @param   string  $errorId    id of error
     * @return  bool
     */
    public function existForWithId($valueName, $errorId)
    {
        return (isset($this->errors[$valueName]) && isset($this->errors[$valueName][$errorId]));
    }

    /**
     * returns list of all errors for all request values
     *
     * @return  array<string,array<string,stubRequestValueError>>
     */
    public function get()
    {
        return $this->errors;
    }

    /**
     * returns a list of errors for given request value
     *
     * @param   string                               $valueName
     * @return  array<string,stubRequestValueError>
     */
    public function getFor($valueName)
    {
        if (isset($this->errors[$valueName]) === true) {
            return $this->errors[$valueName];
        }

        return array();
    }

    /**
     * returns a list of errors for given request value
     *
     * @param   string                 $valueName
     * @param   string                 $errorId    id of error
     * @return  stubRequestValueError
     */
    public function getForWithId($valueName, $errorId)
    {
        if (isset($this->errors[$valueName]) && isset($this->errors[$valueName][$errorId])) {
            return $this->errors[$valueName][$errorId];
        }

        return null;
    }
}
?>