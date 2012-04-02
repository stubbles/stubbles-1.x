<?php
/**
 * Class for validating that something is equal.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubContainsValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Class for validating that something is equal.
 * 
 * This class can compare any scalar value with an expected value. The
 * value to validate has to be of the same type and should have the same
 * content as the expected value.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubContainsValidator extends stubBaseObject implements stubValidator
{
    /**
     * the scalar value to be contained in value to validate
     *
     * @var  string
     */
    protected $contained = null;
    
    /**
     * constructor
     * 
     * @param   scalar|null  $contained
     * @throws  stubIllegalArgumentException
     */
    public function __construct($contained)
    {
        if (is_scalar($contained) == false) {
            throw new stubIllegalArgumentException('Can only check scalar values.');
        }
        
        $this->contained = $contained;
    }

    /**
     * validate that the given value is eqal in content and type to the expected value
     *
     * @param   scalar|null  $value
     * @return  bool         true if value is equal to expected value, else false
     */
    public function validate($value)
    {
        if (is_scalar($value) === false || null === $value) {
            return false;
        }
        
        if (is_bool($this->contained) === true) {
            return ($value === $this->contained);
        }
        
        if ($value === $this->contained || false !== strpos($value, (string) $this->contained)) {
            return true;
        }

        return false;
    }
    
    /**
     * returns a list of criteria for the validator
     * 
     * <code>
     * array('contained' => [contained_value]);
     * </code>
     *
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('contained' => $this->contained);
    }
}
?>