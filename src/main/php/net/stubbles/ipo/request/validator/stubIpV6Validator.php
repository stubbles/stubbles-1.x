<?php
/**
 * Class for validating that something is an ip v6 address.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubIpV6Validator.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Class for validating that something is an ip v6 address.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @since       1.7.0
 */
class stubIpV6Validator extends stubBaseObject implements stubValidator
{
    /**
     * validates if given value is an ip v6 address
     *
     * @param   mixed  $value
     * @return  bool
     */
    public static function validateAddress($value)
    {
        $hexquads = explode(':', $value);
        // Shortest address is ::1, this results in 3 parts...
        if (sizeof($hexquads) < 3) {
            return false;
        }

        if ('' == $hexquads[0]) {
            array_shift($hexquads);
        }

        foreach ($hexquads as $hq) {
            // Catch cases like ::ffaadd00::
            if (strlen($hq) > 4) {
                return false;
            }

            // Not hex
            if (strspn($hq, '0123456789abcdefABCDEF') < strlen($hq)) {
                return false;
            }
        }

        return true;
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