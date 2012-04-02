<?php
/**
 * Utility methods to handle operations based on the uri called in the current request.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
/**
 * Utility methods to handle operations based on the uri called in the current request.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 */
class stubUriRequest extends stubBaseObject
{
    /**
     * current uri
     *
     * @var  string
     */
    protected $current;
    /**
     * condition which lead to selection of processor
     *
     * @var  string
     */
    protected $processorUriCondition = "^/";

    /**
     * constructor
     *
     * @param  string  $current
     */
    public function __construct($current)
    {
        $this->current = $current;
    }

    /**
     * checks if current uri satisfies given uri condition
     *
     * @param   string  $uriCondition
     * @return  bool
     */
    public function satisfies($uriCondition)
    {
        if (null == $uriCondition || preg_match('~' . $uriCondition . '~', $this->current) === 1) {
            return true;
        }

        return false;
    }

    /**
     * sets condition which lead to selection of processor
     *
     * @param   string          $processorUriCondition
     * @return  stubUriRequest
     */
    public function setProcessorUriCondition($processorUriCondition)
    {
        $this->processorUriCondition = $processorUriCondition;
        return $this;
    }

    /**
     * returns part of the uri which was responsible for selection of processor
     *
     * @return  string
     */
    public function getProcessorUri()
    {
        $matches = array();
        preg_match('~(' . $this->processorUriCondition . ')(.*)?~', $this->current, $matches);
        if (isset($matches[1]) === true) {
            return $matches[1];
        }

        return '';
    }

    /**
     * returns remaining uri which was not part of decision for selecting the processor
     *
     * @param   string  $fallback  optional  return this if no remaining uri present
     * @return  string
     */
    public function getRemainingUri($fallback = '')
    {
        $matches = array();
        preg_match('~(' . $this->processorUriCondition . ')([^?]*)?~', $this->current, $matches);
        if (isset($matches[2]) === true && empty($matches[2]) === false) {
            return $matches[2];
        }

        return $fallback;
    }
}
?>