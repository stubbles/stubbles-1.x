<?php
/**
 * Class for filtering dates.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubDateFilter.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubFilter',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::types::stubDate'
);
/**
 * Class for filtering dates.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubDateFilter extends stubBaseObject implements stubFilter
{
    /**
     * request value error factory
     *
     * @var  stubRequestValueErrorFactory
     */
    protected $rveFactory;

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
     * try to change the given value into a date instance
     *
     * @param   string               $value
     * @return  stubDate
     * @throws  stubFilterException  when $value has errors
     */
    public function execute($value)
    {
        if (null == $value) {
            return null;
        }
        
        try {
            return new stubDate($value);
        } catch (stubIllegalArgumentException $iae) {
            throw new stubFilterException($this->rveFactory->create('DATE_INVALID'));
        }
    }
}
?>