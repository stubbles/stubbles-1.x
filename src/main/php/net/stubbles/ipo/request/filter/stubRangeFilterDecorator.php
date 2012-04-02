<?php
/**
 * Basic class for filters on variables of type number.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubRangeFilterDecorator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubStrategyFilterDecorator',
                      'net::stubbles::ipo::request::validator::stubValidator'
);
/**
 * Basic class for filters on variables of type number.
 *
 * This filter takes any value, casts it to float and checks if it complies
 * with the min and/or the max validator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubRangeFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * request value error factory
     *
     * @var  stubRequestValueErrorFactory
     */
    protected $rveFactory;
    /**
     * validator for minimum values
     *
     * @var  stubValidator
     */
    protected $minValidator = null;
    /**
     * the error id to use in case min validation fails
     *
     * @var  string
     */
    protected $minErrorId   = 'VALUE_TOO_SMALL';
    /**
     * validator for maximum values
     *
     * @var  stubValidator
     */
    protected $maxValidator = null;
    /**
     * the error id to use in case max validation fails
     *
     * @var  string
     */
    protected $maxErrorId   = 'VALUE_TOO_GREAT';

    /**
     * constructor
     *
     * @param  stubFilter                    $filter      decorated filter
     * @param  stubRequestValueErrorFactory  $rveFactory  factory to create RequestValueErrors
     */
    public function __construct(stubFilter $filter, stubRequestValueErrorFactory $rveFactory)
    {
        $this->setDecoratedFilter($filter);
        $this->rveFactory   = $rveFactory;
    }

    /**
     * sets the validator for minimum values
     *
     * @param  stubValidator  $minValidator
     * @param  string         $minErrorId    optional  error id to use in case validation fails
     */
    public function setMinValidator(stubValidator $minValidator, $minErrorId = null)
    {
        $this->minValidator = $minValidator;
        if (null !== $minErrorId) {
            $this->minErrorId   = $minErrorId;
        }
    }

    /**
     * returns the validator for minimum values
     *
     * @return  stubValidator
     */
    public function getMinValidator()
    {
        return $this->minValidator;
    }

    /**
     * returns the error id to use in case validation fails
     *
     * @return  string
     */
    public function getMinErrorId()
    {
        return $this->minErrorId;
    }

    /**
     * sets the validator for maximum values
     *
     * @param  stubValidator  $maxValidator
     * @param  string         $maxErrorId    optional  error id to use in case validation fails
     */
    public function setMaxValidator(stubValidator $maxValidator, $maxErrorId = null)
    {
        $this->maxValidator = $maxValidator;
        if (null !== $maxErrorId) {
            $this->maxErrorId   = $maxErrorId;
        }
    }

    /**
     * returns the validator for maximum values
     *
     * @return  stubValidator
     */
    public function getMaxValidator()
    {
        return $this->maxValidator;
    }

    /**
     * returns the error id to use in case validation fails
     *
     * @return  string
     */
    public function getMaxErrorId()
    {
        return $this->maxErrorId;
    }

    /**
     * checks if given value exceeds borders
     *
     * @param   numeric              $value  value to filter
     * @return  numeric              filtered value
     * @throws  stubFilterException  in case $value has errors
     */
    protected function doExecute($value)
    {
        if (null !== $value && null !== $this->minValidator && $this->minValidator->validate($value) !== true) {
             // add error message if input is smaller than minimum value
            throw new stubFilterException($this->rveFactory->create($this->minErrorId)->setValues($this->minValidator->getCriteria()));
        } elseif (null !== $value && null !== $this->maxValidator && $this->maxValidator->validate($value) !== true) {
            // add error message if input is greater than maximum value
            throw new stubFilterException($this->rveFactory->create($this->maxErrorId)->setValues($this->maxValidator->getCriteria()));
        }

        return $value;
    }
}
?>