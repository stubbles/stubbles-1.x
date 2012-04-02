<?php
/**
 * Class for validating that something is an ip v4 address.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubIpV4Validator.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Class for validating that something is an ip v4 address.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @since       1.7.0
 */
class stubIpV4Validator extends stubBaseObject implements stubValidator
{
    /**
     * validates if given value is an ip v4 address
     *
     * @param   mixed  $value
     * @return  bool
     */
    public static function validateAddress($value)
    {
        return (bool) preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $value);
    }

    /**
     * validate that the given value is eqal in content and type to the expected value
     *
     * @param   mixed  $value
     * @return  bool   true if value is equal to expected value, else false
     */
    public function validate($value)
    {
        return self::validateAddress($value);
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