<?php
/**
 * Validator to ensure that a value is not smaller than a given minimum value.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubMinNumberValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Validator to ensure that a value is not smaller than a given minimum value.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubMinNumberValidator extends stubBaseObject implements stubValidator
{
    /**
     * the minimum value to use for validation
     *
     * @var  double
     */
    protected $minValue;

    /**
     * constructor
     *
     * @param  int|double  $minValue  minimum value
     */
    public function __construct($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * returns the minimum value to use for validation
     *
     * @return  double
     */
    public function getValue()
    {
        return $this->minValue;
    }

    /**
     * validate that the given value is greater than or equal to the maximum value
     *
     * @param   int|double  $value
     * @return  bool        true if value is greater than or equal to minimum value, else false
     */
    public function validate($value)
    {
        if ($value < $this->minValue) {
            return false;
        }

        return true;
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('minNumber' => [minimum_value]);
     * </code>
     *
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('minNumber' => $this->minValue);
    }
}
?>