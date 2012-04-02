<?php
/**
 * Validator to validate a value against a list of allowed values.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubPreSelectValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Validator to validate a value against a list of allowed values.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubPreSelectValidator extends stubBaseObject implements stubValidator
{
    /**
     * list of allowed values
     *
     * @var  array
     */
    protected $allowedValues = array();

    /**
     * constructor
     *
     * @param  array  $allowedValues  list of allowed values
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * returns list of allowed values
     *
     * @return  array
     */
    public function getAllowedValues()
    {
        return $this->allowedValues;
    }

    /**
     * validate that the given value is within a list of allowed values
     *
     * @param   mixed  $value
     * @return  bool   true if value is in list of allowed values, else false
     */
    public function validate($value)
    {
        if (!is_array($value)) {
            return in_array($value, $this->allowedValues);
        }
        
        foreach ($value as $val) {
            if (!in_array($val, $this->allowedValues)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('allowedValues' => [array_of_allowed_values]);
     * </code>
     *
     * @return  array<string,array>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('allowedValues' => $this->allowedValues);
    }
}
?>