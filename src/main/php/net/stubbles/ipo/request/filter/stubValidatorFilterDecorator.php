<?php
/**
 * Class for checking values against any validator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubValidatorFilterDecorator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubStrategyFilterDecorator',
                      'net::stubbles::ipo::request::validator::stubValidator'
);
/**
 * Class for checking values against any validator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubValidatorFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * validator to use for the check
     *
     * @var  stubValidator
     */
    protected $validator;
    /**
     * the error id to use in case the validation fails
     *
     * @var  string
     */
    protected $errorId   = 'FIELD_WRONG_VALUE';

    /**
     * constructor
     *
     * @param  stubFilter                    $filter      decorated filter
     * @param  stubRequestValueErrorFactory  $rveFactory  factory to create RequestValueErrors
     * @param  stubValidator                 $validator   validator to use for the check
     */
    public function __construct(stubFilter $filter, stubRequestValueErrorFactory $rveFactory, stubValidator $validator)
    {
        $this->setDecoratedFilter($filter);
        $this->rveFactory = $rveFactory;
        $this->validator  = $validator;
    }

    /**
     * returns the validator
     *
     * @return  stubValidator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * sets the error id to be used
     *
     * @param  string  $errorId
     */
    public function setErrorId($errorId)
    {
        $this->errorId = $errorId;
    }

    /**
     * returns the error id to be used
     *
     * @return  string
     */
    public function getErrorId()
    {
        return $this->errorId;
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
        
        if ($this->validator->validate($value) === false) {
            throw new stubFilterException($this->rveFactory->create($this->errorId));
        }

        return $value;
    }
}
?>