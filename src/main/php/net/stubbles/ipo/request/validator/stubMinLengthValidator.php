<?php
/**
 * Validator to ensure that a string is not shorter than a given minimum length.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubMinLengthValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Validator to ensure that a string is not shorter than a given minimum length.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubMinLengthValidator extends stubBaseObject implements stubValidator
{
    /**
     * the minimum length to use for validation
     *
     * @var  int
     */
    protected $minLength;

    /**
     * constructor
     *
     * @param  int  $minLength  minimum length
     */
    public function __construct($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * returns the minimum length to use for validation
     *
     * @return  int
     */
    public function getValue()
    {
        return $this->minLength;
    }

    /**
     * validate that the given value is not shorter than the maximum length
     *
     * @param   string  $value
     * @return  bool    true if value is not shorter than minimum length, else false
     */
    public function validate($value)
    {
        if (iconv_strlen($value) < $this->minLength) {
            return false;
        }

        return true;
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('minLength' => [min_length_of_string]);
     * </code>
     *
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('minLength' => $this->minLength);
    }
}
?>