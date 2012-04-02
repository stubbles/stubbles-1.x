<?php
/**
 * Class for filtering passwords.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubPasswordFilter.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter',
                      'net::stubbles::php::string::stubStringEncoder',
                      'net::stubbles::ipo::request::validator::stubValidator'
);
/**
 * Class for filtering passwords.
 *
 * This filter allows to check password inputs and if they comply with the rules
 * for a password. It is possible to check against a list of non-allowed passwords
 * (e.g. the username or the login name).
 * If the value is an array the fields with key 0 and 1 are compared. If they are
 * not equal the password is not allowed (can be used to prevent mistyped
 * passwords in register or password change forms).
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubPasswordFilter extends stubBaseObject implements stubFilter
{
    /**
     * request value error factory
     *
     * @var  stubRequestValueErrorFactory
     */
    protected $rveFactory;
    /**
     * minimum amount of different characters in the password
     *
     * @var  int
     */
    protected $minDiffChars     = 5;
    /**
     * list of values that are not allowed as password
     *
     * @var  array
     */
    protected $nonAllowedValues = array();

    /**
     * constructor
     *
     * @param  stubRequestValueErrorFactory  $rveFactory  factory to create stubRequestValueErrors
     */
    public function __construct(stubRequestValueErrorFactory $rveFactory)
    {
        $this->rveFactory = $rveFactory;
    }

    /**
     * set a list of values that are not allowed as password
     *
     * @param  array  $values  list of values that are not allowed as password
     */
    public function nonAllowedValues(array $values)
    {
        $this->nonAllowedValues = $values;
    }

    /**
     * returns a list of values that are not allowed as password
     *
     * @return  array
     */
    public function getNonAllowedValues()
    {
        return $this->nonAllowedValues;
    }

    /**
     * set minimum amount of different characters within password
     * 
     * Set the value with NULL to disable the check.
     *
     * @param  int  $minDiffChars
     */
    public function minDiffChars($minDiffChars)
    {
        $this->minDiffChars = $minDiffChars;
    }

    /**
     * return the minimum amount of different characters within the password
     *
     * @return  int
     */
    public function getMinDiffChars()
    {
        return $this->minDiffChars;
    }

    /**
     * check if entered passwords fulfill password conditions
     *
     * @param   array|string         $value  two passwords in an array or one password as string
     * @return  string               secured password
     * @throws  stubFilterException  when $value has errors
     */
    public function execute($value)
    {
        if (is_array($value) === true) {
            if ($value[0] !== $value[1]) {
                throw new stubFilterException($this->rveFactory->create('PASSWORDS_NOT_EQUAL'));
            }

            $value = $value[0];
        }

        if (in_array($value, $this->nonAllowedValues) === true) {
            throw new stubFilterException($this->rveFactory->create('PASSWORD_INVALID'));
        }
        
        if (null !== $this->minDiffChars) {
            if (count(count_chars($value, 1)) < $this->minDiffChars) {
                throw new stubFilterException($this->rveFactory->create('PASSWORD_TOO_LESS_DIFF_CHARS'));
            }
        }

        if (strlen($value) > 0) {
            return $value;
        }
        
        return null;
    }
}
?>