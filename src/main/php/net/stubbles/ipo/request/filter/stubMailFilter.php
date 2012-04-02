<?php
/**
 * Class for filtering mail addresses.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubMailFilter.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter',
                      'net::stubbles::ipo::request::validator::stubMailValidator'
);
/**
 * Class for filtering mail addresses.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubMailFilter extends stubBaseObject implements stubFilter
{
    /**
     * request value error factory
     *
     * @var  stubRequestValueErrorFactory
     */
    protected $rveFactory;
    /**
     * validator to use for checking the mail address
     *
     * @var  stubValidator
     */
    protected $mailValidator;

    /**
     * constructor
     *
     * @param  stubRequestValueErrorFactory  $rveFactory     factory to create stubRequestValueErrors
     */
    public function __construct(stubRequestValueErrorFactory $rveFactory)
    {
        $this->rveFactory = $rveFactory;
    }

    /**
     * use another validator
     *
     * @param  stubValidator    $validator
     * @return  stubMailFilter
     */
    public function usingValidator(stubValidator $validator)
    {
        $this->mailValidator = $validator;
        return $this;
    }

    /**
     * check if entered passwords fulfill password conditions
     *
     * @param   array|string         $value  the mail addressto check
     * @return  string               the checked mail address to check
     * @throws  stubFilterException  in case $value has errors
     */
    public function execute($value)
    {
        if (strlen($value) === 0) {
            return null;
        }
        
        if (null === $this->mailValidator) {
            $this->mailValidator = new stubMailValidator();
        }
        
        if ($this->mailValidator->validate($value) === true) {
            return $value;
        }
        
        //    check for spaces
        if (preg_match('/\s/i', $value) != false) {
            throw new stubFilterException($this->rveFactory->create('MAILADDRESS_CANNOT_CONTAIN_SPACES'));
        }

        //    check for German umlaut
        if (preg_match('/[����]/i', $value) != false) {
            throw new stubFilterException($this->rveFactory->create('MAILADDRESS_CANNOT_CONTAIN_UMLAUTS'));
        }

        //    check for more than one '@'
        if (substr_count($value, '@') != 1) {
            throw new stubFilterException($this->rveFactory->create('MAILADDRESS_MUST_CONTAIN_ONE_AT'));
        }

        //    check for valid chars in email
        if (preg_match('/^[' . preg_quote('abcdefghijklmnopqrstuvwxyz1234567890@.+_-') . ']+$/iD', $value) == false) {
            throw new stubFilterException($this->rveFactory->create('MAILADDRESS_CONTAINS_ILLEGAL_CHARS'));
        }
        
        if (strpos($value, '..') !== false) {
            throw new stubFilterException($this->rveFactory->create('MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS'));
        }
        
        throw new stubFilterException($this->rveFactory->create('MAILADDRESS_INCORRECT'));
    }
}
?>