<?php
/**
 * Base class for filtering strings.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubLengthFilterDecorator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubStrategyFilterDecorator',
                      'net::stubbles::ipo::request::validator::stubValidator'
);
/**
 * Base class for filtering strings.
 *
 * This is a base class for string filtering. It provides methods to check the
 * minimum and maximum length of a string using validators, but both are
 * optional checks.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubLengthFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * request value error factory
     *
     * @var  stubRequestValueErrorFactory
     */
    protected $rveFactory;
    /**
     * validator for minimum length of string
     *
     * @var  stubValidator
     */
    protected $minLength        = null;
    /**
     * the error id to use in case min length validation fails
     *
     * @var  string
     */
    protected $minLengthErrorId = 'STRING_TOO_SHORT';
    /**
     * validator for maximum length of string
     *
     * @var  stubValidator
     */
    protected $maxLength   = null;
    /**
     * the error id to use in case max length validation fails
     *
     * @var  string
     */
    protected $maxLengthErrorId = 'STRING_TOO_LONG';

    /**
     * constructor
     *
     * @param  stubFilter                    $filter      decorated filter
     * @param  stubRequestValueErrorFactory  $rveFactory  factory to create RequestValueErrors
     */
    public function __construct(stubFilter $filter, stubRequestValueErrorFactory $rveFactory)
    {
        $this->setDecoratedFilter($filter);
        $this->rveFactory = $rveFactory;
    }

    /**
     * set a min length validator
     *
     * @param  stubValidator  $minLength
     * @param  string         $minLengthErrorId  optional  error id to use in case validation fails
     */
    public function setMinLengthValidator(stubValidator $minLength, $minLengthErrorId = null)
    {
        $this->minLength        = $minLength;
        if (null !== $minLengthErrorId) {
            $this->minLengthErrorId = $minLengthErrorId;
        }
    }

    /**
     * returns the min length validator
     *
     * @return  stubValidator
     */
    public function getMinLengthValidator()
    {
        return $this->minLength;
    }

    /**
     * returns the error id to use in case validation fails
     *
     * @return  string
     */
    public function getMinLengthErrorId()
    {
        return $this->minLengthErrorId;
    }

    /**
     * set a max length validator
     *
     * @param  stubValidator  $maxLength
     * @param  string         $maxLengthErrorId  optional  error id to use in case validation fails
     */
    public function setMaxLengthValidator(stubValidator $maxLength, $maxLengthErrorId = null)
    {
        $this->maxLength = $maxLength;
        if (null !== $maxLengthErrorId) {
            $this->maxLengthErrorId = $maxLengthErrorId;
        }
    }

    /**
     * returns the max length validator
     *
     * @return  stubValidator
     */
    public function getMaxLengthValidator()
    {
        return $this->maxLength;
    }

    /**
     * returns the error id to use in case validation fails
     *
     * @return  string
     */
    public function getMaxLengthErrorId()
    {
        return $this->maxLengthErrorId;
    }

    /**
     * execute the filter
     *
     * @param   string  $value
     * @return  string
     * @throws  stubFilterException
     */
    protected function doExecute($value)
    {
        if (strlen($value) === 0) {
            return null;
        }
        
        if (null != $this->minLength && $this->minLength->validate($value) === false) {
            // input is shorter than maximal allowed length
            throw new stubFilterException($this->rveFactory->create($this->minLengthErrorId)->setValues($this->minLength->getCriteria()));
        } elseif (null != $this->maxLength && $this->maxLength->validate($value) === false) {
            // input is longer than maximal allowed length
            throw new stubFilterException($this->rveFactory->create($this->maxLengthErrorId)->setValues($this->maxLength->getCriteria()));
        }
        
        return $value;
    }
}
?>