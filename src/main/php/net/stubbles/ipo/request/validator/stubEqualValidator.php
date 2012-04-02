<?php
/**
 * Class for validating that something is equal.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubEqualValidator.php 2857 2011-01-10 13:43:39Z mikey $
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
class stubEqualValidator extends stubBaseObject implements stubValidator
{
    /**
     * the expected password
     *
     * @var  string
     */
    protected $expected = null;
    
    /**
     * constructor
     * 
     * @param   scalar|null  $expected
     * @throws  stubIllegalArgumentException
     */
    public function __construct($expected)
    {
        if (is_scalar($expected) == false && null != $expected) {
            throw new stubIllegalArgumentException('Can only compare scalar values and null.');
        }
        
        $this->expected = $expected;
    }

    /**
     * validate that the given value is eqal in content and type to the expected value
     *
     * @param   scalar|null  $value
     * @return  bool         true if value is equal to expected value, else false
     */
    public function validate($value)
    {
        if ($this->expected !== $value) {
            return false;
        }

        return true;
    }
    
    /**
     * returns a list of criteria for the validator
     * 
     * <code>
     * array('expected' => [expected_value]);
     * </code>
     *
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('expected' => $this->expected);
    }
}
?>