<?php
/**
 * Filters on request variables of type double / float.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubFloatFilter.php 2320 2009-09-14 08:34:11Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter');
/**
 * Filters on request variables of type double / float.
 * 
 * This filter takes any value and casts it to float. Afterwards its multiplied
 * with 10^$decimals to get an integer value which can be used for mathematical
 * operations for accuracy. If no value for x is given the value to filter is
 * returned as is after the cast.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubFloatFilter extends stubBaseObject implements stubFilter
{
    /**
     * number of decimals
     *
     * @var  int
     */
    protected $decimals = null;

    /**
     * sets number of decimals
     *
     * @param   int              $decimals
     * @return  stubFloatFilter
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * returns number of decimals
     *
     * @return  int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * checks if given value is double
     *
     * @param   mixed      $value  value to filter
     * @return  int|float
     */
    function execute($value)
    {
        if (null === $value) {
            return null;
        }
        
        settype($value, 'float');
        if (null == $this->decimals) {
            return $value;
        }
        
        return (int) ($value * pow(10, $this->decimals));
    }
}
?>