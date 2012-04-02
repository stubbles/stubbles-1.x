<?php
/**
 * Class for validating that something is an ip address, either v4 or v6.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubIpValidator.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator',
                      'net::stubbles::ipo::request::validator::stubIpV4Validator',
                      'net::stubbles::ipo::request::validator::stubIpV6Validator'
);
/**
 * Class for validating that something is an ip address, either v4 or v6.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubIpValidator extends stubBaseObject implements stubValidator
{
    /**
     * validate that the given value is an ip address (either v4 or v6)
     *
     * @param   mixed  $value
     * @return  bool   true if value is equal to expected value, else false
     */
    public function validate($value)
    {
        if (stubIpV4Validator::validateAddress($value) === true) {
            return true;
        }

        return stubIpV6Validator::validateAddress($value);
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
        return array();
    }
}
?>