<?php
/**
 * Builder to create filters.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubFilterBuilder.php 2331 2009-09-16 18:12:47Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubAbstractFilterDecorator',
                      'net::stubbles::ipo::request::filter::stubStrategyFilterDecorator',
                      'net::stubbles::lang::types::stubDate'
);
/**
 * Builder to create filters.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubFilterBuilder extends stubAbstractFilterDecorator
{
    /**
     * constructor
     *
     * @param  stubFilter                    $filter
     * @param  stubRequestValueErrorFactory  $rveFactory
     */
    public function __construct(stubFilter $filter, stubRequestValueErrorFactory $rveFactory)
    {
        $this->setDecoratedFilter($filter);
        $this->rveFactory = $rveFactory;
    }

    /**
     * sets the request error value factory to be used by the filter
     *
     * @param   stubRequestValueErrorFactory  $rveFactory
     * @return  stubFilterBuilder
     */
    public function using(stubRequestValueErrorFactory $rveFactory)
    {
        $this->rveFactory = $rveFactory;
        return $this;
    }

    /**
     * returns the request error value factory to be used by the filter
     *
     * @return  stubRequestValueErrorFactory
     */
    public function getRveFactory()
    {
        return $this->rveFactory;
    }

    /**
     * decorates the filter with a range filter
     *
     * To create a lower border only use NULL for $max, to create an upper
     * border only use NULL for $min.
     *
     * @param   numeric            $min
     * @param   numeric            $max
     * @param   string             $minErrorId  optional  error id for failing min validation
     * @param   string             $maxErrorId  optional  error id for failing max validation
     * @param   int                $strategy    optional  strategy to be used: before or after decorated filter
     * @return  stubFilterFactory
     */
    public function inRange($min, $max, $minErrorId = null, $maxErrorId = null, $strategy = stubStrategyFilterDecorator::STRATEGY_AFTER)
    {
        if (null !== $min || null !== $max) {
            stubClassLoader::load('net::stubbles::ipo::request::filter::stubRangeFilterDecorator',
                                  'net::stubbles::ipo::request::validator::stubMinNumberValidator',
                                  'net::stubbles::ipo::request::validator::stubMaxNumberValidator'
            );
            $filter = new stubRangeFilterDecorator($this->getDecoratedFilter(), $this->rveFactory);
            if (null !== $min) {
                $filter->setMinValidator(new stubMinNumberValidator($min), $minErrorId);
            }
            
            if (null !== $max) {
                $filter->setMaxValidator(new stubMaxNumberValidator($max), $maxErrorId);
            }
            
            $filter->setStrategy($strategy);
            $this->setDecoratedFilter($filter);
        }
        
        return $this;
    }

    /**
     * decorates the filter with a length filter
     *
     * To create a lower border only use NULL for $maxLength, to create an upper
     * border only use NULL for $minLength.
     *
     * @param   numeric            $minLength
     * @param   numeric            $maxLength
     * @param   string             $minLengthErrorId  optional  error id for failing min validation
     * @param   string             $maxLengthErrorId  optional  error id for failing max validation
     * @param   int                $strategy          optional  strategy to be used: before or after decorated filter
     * @return  stubFilterFactory
     */
    public function length($minLength, $maxLength, $minLengthErrorId = null, $maxLengthErrorId = null, $strategy = stubStrategyFilterDecorator::STRATEGY_AFTER)
    {
        if (null !== $minLength || null !== $maxLength) {
            stubClassLoader::load('net::stubbles::ipo::request::filter::stubLengthFilterDecorator',
                                  'net::stubbles::ipo::request::validator::stubMinLengthValidator',
                                  'net::stubbles::ipo::request::validator::stubMaxLengthValidator'
            );
            $filter = new stubLengthFilterDecorator($this->getDecoratedFilter(), $this->rveFactory);
            if (null !== $minLength) {
                $filter->setMinLengthValidator(new stubMinLengthValidator($minLength), $minLengthErrorId);
            }
            
            if (null !== $maxLength) {
                $filter->setMaxLengthValidator(new stubMaxLengthValidator($maxLength), $maxLengthErrorId);
            }
            
            $filter->setStrategy($strategy);
            $this->setDecoratedFilter($filter);
        }
        
        return $this;
    }

    /**
     * decorates the filter with a period filter
     *
     * @param   stubDate           $minDate         optional
     * @param   stubDate           $maxDate         optional
     * @param   string             $minDateErrorId  optional  error id for failing min validation
     * @param   string             $maxDateErrorId  optional  error id for failing max validation
     * @param   string             $dateFormat      optional  format of date in error messages
     * @return  stubFilterFactory
     */
    public function inPeriod(stubDate $minDate = null, stubDate $maxDate = null, $minDateErrorId = null, $maxDateErrorId = null, $dateFormat = null)
    {
        if (null !== $minDate || null !== $maxDate) {
            stubClassLoader::load('net::stubbles::ipo::request::filter::stubPeriodFilterDecorator');
            $filter = new stubPeriodFilterDecorator($this->getDecoratedFilter(), $this->rveFactory);
            if (null !== $minDate) {
                $filter->setMinDate($minDate, $minDateErrorId);
            }
            
            if (null !== $maxDate) {
                $filter->setMaxDate($maxDate, $maxDateErrorId);
            }
            
            if (null != $dateFormat) {
                $filter->setDateFormat($dateFormat);
            }
            
            $this->setDecoratedFilter($filter);
        }
        
        return $this;
    }

    /**
     * decorates the filter as required
     *
     * @param   string             $errorId   optional
     * @param   int                $strategy  optional  strategy to be used: before or after decorated filter
     * @return  stubFilterFactory
     */
    public function asRequired($errorId = null, $strategy = stubStrategyFilterDecorator::STRATEGY_BEFORE)
    {
        stubClassLoader::load('net::stubbles::ipo::request::filter::stubRequiredFilterDecorator');
        $filter = new stubRequiredFilterDecorator($this->getDecoratedFilter(), $this->rveFactory);
        if (null != $errorId) {
            $filter->setErrorId($errorId);
        }
        
        $filter->setStrategy($strategy);
        $this->setDecoratedFilter($filter);
        return $this;
    }

    /**
     * decorates the filter with a default value
     *
     * @param   mixed              $defaultValue
     * @param   int                $strategy      optional  strategy to be used: before or after decorated filter
     * @return  stubFilterFactory
     */
    public function defaultsTo($defaultValue, $strategy = stubStrategyFilterDecorator::STRATEGY_AFTER)
    {
        if (null === $defaultValue) {
            return $this;
        }
        
        stubClassLoader::load('net::stubbles::ipo::request::filter::stubDefaultValueFilterDecorator');
        $filter = new stubDefaultValueFilterDecorator($this->getDecoratedFilter(), $defaultValue);
        $filter->setStrategy($strategy);
        $this->setDecoratedFilter($filter);
        return $this;
    }

    /**
     * decorates the filter with a validator
     *
     * @param   stubValidator      $validator
     * @param   string             $errorId    optional
     * @param   int                $strategy   optional  strategy to be used: before or after decorated filter
     * @return  stubFilterFactory
     */
    public function validatedBy(stubValidator $validator, $errorId = null, $strategy = stubStrategyFilterDecorator::STRATEGY_AFTER)
    {
        stubClassLoader::load('net::stubbles::ipo::request::filter::stubValidatorFilterDecorator');
        $filter = new stubValidatorFilterDecorator($this->getDecoratedFilter(), $this->rveFactory, $validator);
        if (null != $errorId) {
            $filter->setErrorId($errorId);
        }
        
        $filter->setStrategy($strategy);
        $this->setDecoratedFilter($filter);
        return $this;
    }

    /**
     * decorates the filter with an encoder
     *
     * @param   stubStringEncoder  $encoder
     * @param   int                $strategy  optional  strategy to be used: before or after decorated filter
     * @return  stubFilterFactory
     */
    public function encodedWith(stubStringEncoder $encoder, $strategy = stubStrategyFilterDecorator::STRATEGY_AFTER)
    {
        $this->codedWith($encoder, stubStringEncoder::MODE_ENCODE, $strategy);
        return $this;
    }

    /**
     * decorates the filter with a decoder
     *
     * @param   stubStringEncoder  $encoder
     * @param   int                $strategy  optional  strategy to be used: before or after decorated filter
     * @return  stubFilterFactory
     */
    public function decodedWith(stubStringEncoder $encoder, $strategy = stubStrategyFilterDecorator::STRATEGY_AFTER)
    {
        $this->codedWith($encoder, stubStringEncoder::MODE_DECODE, $strategy);
        return $this;
    }

    /**
     * decorates the filter with an encoder
     *
     * @param   stubStringEncoder  $encoder
     * @param   int                $encoderMode  optional
     * @param   int                $strategy     optional  strategy to be used: before or after decorated filter
     * @return  stubFilterFactory
     */
    protected function codedWith(stubStringEncoder $encoder, $encoderMode = stubStringEncoder::MODE_DECODE, $strategy = stubStrategyFilterDecorator::STRATEGY_AFTER)
    {
        stubClassLoader::load('net::stubbles::ipo::request::filter::stubEncodingFilterDecorator');
        $filter = new stubEncodingFilterDecorator($this->getDecoratedFilter(), $encoder, $encoderMode);
        $filter->setStrategy($strategy);
        $this->setDecoratedFilter($filter);
    }
}
?>