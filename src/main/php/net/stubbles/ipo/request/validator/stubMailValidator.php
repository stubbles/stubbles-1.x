<?php
/**
 * Validator to ensure that a string is a mail address.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubMailValidator.php 2404 2009-12-07 19:05:53Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Validator to ensure that a string is a mail address.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubMailValidator extends stubBaseObject implements stubValidator
{
    /**
     * validate that the given value is not longer than the maximum length
     *
     * @param   string  $value
     * @return  bool    true if value is not longer than maximal length, else false
     */
    public function validate($value)
    {
        if (null == $value || strlen($value) == 0) {
            return false;
        }
        
        $url = @parse_url('mailto://' . $value);
        if (isset($url['host']) === false || preg_match('/^([a-zA-Z0-9-]*)\.([a-zA-Z]{2,4})$/', $url['host']) == false) {
            return false;
        }
        
        if (isset($url['user']) === false || strlen($url['user']) == 0 || preg_match('/^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*$/', $url['user']) == false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * returns a list of criteria for the validator
     * 
     * <code>
     * array();
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