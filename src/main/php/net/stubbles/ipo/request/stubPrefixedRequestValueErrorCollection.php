<?php
/**
 * Value error list which decorates another one allowing access only to prefixed value names.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubPrefixedRequestValueErrorCollection.php 2637 2010-08-14 18:25:37Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorCollection');
/**
 * Value error list which decorates another one allowing access only to prefixed value names.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @since       1.3.0
 */
class stubPrefixedRequestValueErrorCollection extends stubBaseObject implements stubRequestValueErrorCollection
{
    /**
     * decorated instance
     *
     * @var  stubRequestValueErrorCollections
     */
    protected $requestValueErrorCollection;
    /**
     * prefix for request value names
     *
     * @var  string
     */
    protected $prefix;

    /**
     * constructor
     *
     * @param  stubRequestValueErrorCollection  $requestValueErrorCollection
     * @param  string                           $prefix
     */
    public function __construct(stubRequestValueErrorCollection $requestValueErrorCollection, $prefix)
    {
        $this->requestValueErrorCollection = $requestValueErrorCollection;
        $this->prefix                      = $prefix;
    }

    /**
     * helper method to calculate the prefixed value name
     *
     * @param   string  $valueName
     * @return  string
     */
    protected function getPrefixedValueName($valueName)
    {
        return $this->prefix . '_' . $valueName;
    }

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
        return $this->requestValueErrorCollection->add($valueError,
                                                       $this->getPrefixedValueName($valueName)
               );
    }

    /**
     * returns number of collected errors
     *
     * @return  int
     */
    public function count()
    {
        return count($this->get());
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
        return $this->requestValueErrorCollection->existFor($this->getPrefixedValueName($valueName));
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
        return $this->requestValueErrorCollection->existForWithId($this->getPrefixedValueName($valueName),
                                                                  $errorId
               );
    }

    /**
     * returns list of all errors for all request values
     *
     * @return  array<string,array<string,stubRequestValueError>>
     */
    public function get()
    {
        if ($this->requestValueErrorCollection->exist() === false) {
            return array();
        }

        $returnedErrors = array();
        $checkLength    = strlen($this->prefix) + 1;
        foreach ($this->requestValueErrorCollection->get() as $valueName => $valueErrors) {
            if (substr($valueName, 0, $checkLength) === $this->prefix . '_') {
                $returnedErrors[str_replace($this->prefix . '_', '', $valueName)] = $valueErrors;
            }
        }

        return $returnedErrors;
    }

    /**
     * returns a list of errors for given request value
     *
     * @param   string                               $valueName
     * @return  array<string,stubRequestValueError>
     */
    public function getFor($valueName)
    {
        return $this->requestValueErrorCollection->getFor($this->getPrefixedValueName($valueName));
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
        return $this->requestValueErrorCollection->getForWithId($this->getPrefixedValueName($valueName),
                                                                $errorId
               );
    }
}
?>