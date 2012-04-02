<?php
/**
 * Validator to ensure a value complies to a given regular expression.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubRegexValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Validator to ensure a value complies to a given regular expression.
 *
 * The validator uses preg_match() and checks if the value occurs exactly
 * one time. Please make sure that the supplied regular expresion contains
 * correct delimiters, they will not be applied automatically. The validate()
 * method throws a runtime exception in case the regular expression is invalid.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubRegexValidator extends stubBaseObject implements stubValidator
{
    /**
     * the regular expression to use for validation
     *
     * @var  string
     */
    protected $regex;

    /**
     * constructor
     *
     * @param  string  $regex  regular expression to use for validation
     */
    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    /**
     * returns the regular expression to use for validation
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->regex;
    }

    /**
     * validate that the given value complies with the regular expression
     *
     * @param   mixed  $value
     * @return  bool   true if value complies with regular expression, else false
     * @throws  stubRuntimeException  in case the used regular expresion is invalid
     */
    public function validate($value)
    {
        $check = @preg_match($this->regex, $value);
        if (false === $check) {
            throw new stubRuntimeException('Invalid regular expression ' . $this->regex);
        }
        
        return ((1 != $check) ? (false) : (true));
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('regex' => [regular_expression]);
     * </code>
     *
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('regex' => $this->regex);
    }
}
?>