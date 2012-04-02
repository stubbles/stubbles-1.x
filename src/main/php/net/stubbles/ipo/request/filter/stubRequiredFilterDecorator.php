<?php
/**
 * Class for filtering if a value is empty or not.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubRequiredFilterDecorator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubStrategyFilterDecorator'
);
/**
 * Class for filtering if a value is empty or not.
 *
 * If the value is empty an error will be created.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubRequiredFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * request value error factory
     *
     * @var  stubRequestValueErrorFactory
     */
    protected $rveFactory;
    /**
     * error id to be used
     *
     * @var  string
     */
    protected $errorId    = 'FIELD_EMPTY';

    /**
     * constructor
     *
     * @param  stubFilter                    $filter      decorated filter
     * @param  stubRequestValueErrorFactory  $rveFactory  factory to create RequestValueErrors
     * @param  int                           $stategy     optional
     */
    public function __construct(stubFilter $filter, stubRequestValueErrorFactory $rveFactory, $stategy = stubStrategyFilterDecorator::STRATEGY_BEFORE)
    {
        $this->rveFactory = $rveFactory;
        $this->setDecoratedFilter($filter);
        $this->setStrategy($stategy);
    }

    /**
     * sets the id of the request value error to be used
     *
     * @param  string  $errorId
     */
    public function setErrorId($errorId)
    {
        $this->errorId = $errorId;
    }

    /**
     * returns the id of the request value error to be used
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
        if ((null == $value || strlen($value) == 0)) {
            throw new stubFilterException($this->rveFactory->create($this->errorId));
        }
        
        return $value;
    }
}
?>