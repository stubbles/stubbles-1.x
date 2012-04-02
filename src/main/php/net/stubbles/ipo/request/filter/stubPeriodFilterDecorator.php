<?php
/**
 * Filter to check if a date is inbetween a certain period.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubPeriodFilterDecorator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubStrategyFilterDecorator',
                      'net::stubbles::lang::types::stubDate'
);
/**
 * Filter to check if a date is inbetween a certain period.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubPeriodFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * request value error factory
     *
     * @var  stubRequestValueErrorFactory
     */
    protected $rveFactory;
    /**
     * smallest allowed date
     *
     * @var  stubDate
     */
    protected $minDate        = null;
    /**
     * the error id to use in case min date validation fails
     *
     * @var  string
     */
    protected $minDateErrorId = 'DATE_TOO_EARLY';
    /**
     * greatest allowed date
     *
     * @var  stubDate
     */
    protected $maxDate        = null;
    /**
     * the error id to use in case max date validation fails
     *
     * @var  string
     */
    protected $maxDateErrorId = 'DATE_TOO_LATE';
    /**
     * format of date in error messages
     *
     * @var  string
     */
    protected $dateFormat     = 'Y-m-d';

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
     * @param  stubDate  $minDate
     * @param  string    $minDateErrorId  optional  error id to use in case validation fails
     */
    public function setMinDate(stubDate $minDate, $minDateErrorId = null)
    {
        $this->minDate = $minDate;
        if (null !== $minDateErrorId) {
            $this->minDateErrorId = $minDateErrorId;
        }
    }

    /**
     * returns the min date
     *
     * @return  stubDate
     */
    public function getMinDate()
    {
        return $this->minDate;
    }

    /**
     * returns the error id to use in case validation fails
     *
     * @return  string
     */
    public function getMinDateErrorId()
    {
        return $this->minDateErrorId;
    }

    /**
     * set a max length validator
     *
     * @param  stubDate  $maxDate
     * @param  string    $maxDateErrorId  optional  error id to use in case validation fails
     */
    public function setMaxDate(stubDate $maxDate, $maxDateErrorId = null)
    {
        $this->maxDate = $maxDate;
        if (null !== $maxDateErrorId) {
            $this->maxDateErrorId = $maxDateErrorId;
        }
    }

    /**
     * returns the max date
     *
     * @return  stubDate
     */
    public function getMaxDate()
    {
        return $this->maxDate;
    }

    /**
     * returns the error id to use in case validation fails
     *
     * @return  string
     */
    public function getMaxDateErrorId()
    {
        return $this->maxDateErrorId;
    }

    /**
     * sets the way the date will be formatted in error messages
     *
     * @param  string  $dateFormat
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * returns format of date in error messages
     *
     * @return  string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
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
        if (($value instanceof stubDate) === false) {
            return null;
        }

        if (null != $this->minDate && $this->minDate->isAfter($value) === true) {
            throw new stubFilterException($this->rveFactory->create($this->minDateErrorId)->setValues(array('earliestDate' => $this->minDate->format($this->dateFormat))));
        } elseif (null != $this->maxDate && $this->maxDate->isBefore($value) === true) {
            throw new stubFilterException($this->rveFactory->create($this->maxDateErrorId)->setValues(array('latestDate' => $this->maxDate->format($this->dateFormat))));
        }

        return $value;
    }
}
?>