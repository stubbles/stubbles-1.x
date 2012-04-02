<?php
/**
 * Decorator that is able to execute the decorating behaviour before or after
 * the decorated filter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubStrategyFilterDecorator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubAbstractFilterDecorator',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubIllegalStateException'
);
/**
 * Decorator that is able to execute the decorating behaviour before or after
 * the decorated filter.
 *
 * Default strategy is to execute the decorating behaviour after the decorated
 * filter was executed.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
abstract class stubStrategyFilterDecorator extends stubAbstractFilterDecorator
{
    /**
     * indicates the decorator behavior:
     * 
     * decorate the filter *before* the actual method call.
     */
    const STRATEGY_BEFORE = -1;
    /**
     * indicates the decorator behavior:
     * 
     * decorate the filter *after* the actual method call.
     */
    const STRATEGY_AFTER  = 1;
    /**
     * decorator Strategy:
     * 
     * <ul>
     *   <li>stubStrategyFilterDecorator::STRATEGY_BEFORE</li>
     *   <li>stubStrategyFilterDecorator::STRATEGY_AFTER</li>
     * </ul>
     *
     * @var  int
     */
    protected $strategy   = self::STRATEGY_AFTER;

    /**
     * sets the strategy to be applied
     *
     * @param   int  $strategy
     * @throws  stubIllegalArgumentException
     */
    public function setStrategy($strategy)
    {
        if (in_array($strategy, array(self::STRATEGY_BEFORE, self::STRATEGY_AFTER)) === false) {
            throw new stubIllegalArgumentException('Invalid strategy type ' . $strategy);
        }
        
        $this->strategy = $strategy;
    }

    /**
     * execute the filter
     *
     * @param   string                     $value
     * @return  string
     * @throws  stubIllegalStateException
     */
    public function execute($value)
    {
        $result = null;
        switch ($this->strategy) {
            case self::STRATEGY_BEFORE:
                $result = $this->getDecoratedFilter()->execute($this->doExecute($value));
                break;

            case self::STRATEGY_AFTER:
                $result = $this->doExecute($this->getDecoratedFilter()->execute($value));
                break;

            default:
                throw new stubIllegalStateException('Invalid strategy type ' . $this->strategy);
        }
        
        return $result;
    }

    /**
     * execute the filter
     *
     * @param   string  $value
     * @return  string
     */
    protected abstract function doExecute($value);
}
?>