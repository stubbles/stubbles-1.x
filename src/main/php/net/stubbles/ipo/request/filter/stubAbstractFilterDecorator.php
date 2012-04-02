<?php
/**
 * Base class for filter decorators: delegates everything to the decorated filter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubAbstractFilterDecorator.php 2327 2009-09-16 14:27:22Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter',
                      'net::stubbles::lang::exceptions::stubMethodNotSupportedException'
);
/**
 * Base class for filter decorators: delegates everything to the decorated filter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
abstract class stubAbstractFilterDecorator extends stubBaseObject implements stubFilter
{
    /**
     * decorated filter
     *
     * @var  stubFilter
     */
    protected $decoratedFilter;

    /**
     * setter method
     *
     * @param  stubFilter  $decoratedFilter
     */
    public function setDecoratedFilter($decoratedFilter)
    {
        $this->decoratedFilter = $decoratedFilter;
    }

    /**
     * getter method
     *
     * @return  stubFilter
     */
    public function getDecoratedFilter()
    {
        return $this->decoratedFilter;
    }

    /**
     * execute the filter
     *
     * @param   mixed  $value  value to filter
     * @return  mixed
     */
    public function execute($value)
    {
        return $this->getDecoratedFilter()->execute($value);
    }

    /**
     * interceptor for method calls on filters without direct support
     *
     * @param   string             $method     name of the method to call
     * @param   array              $arguments  list of arguments for the method
     * @return  stubFilterFactory
     * 
     */
    public function __call($method, $arguments)
    {
        $result = $this->callRecursive($this->getDecoratedFilter(), $method, $arguments);
        if (null === $result) {
            return $this;
        }
        
        return $result;
    }

    /**
     * helper method to recurse down to the base filter in order to call a method
     *
     * @param   stubFilter  $filter     filter to try to call the method off
     * @param   string      $method     name of the method to call
     * @param   array       $arguments  list of arguments for the method
     * @return  mixed
     * @throws  stubMethodNotSupportedException
     */
    protected function callRecursive(stubFilter $filter, $method, $arguments)
    {
        if (method_exists($filter, $method) === true) {
            return call_user_func_array(array($filter, $method), $arguments);
        }
        
        if (method_exists($filter, 'getDecoratedFilter') === true) {
            return $this->callRecursive($filter->getDecoratedFilter(), $method, $arguments);
        }
        
        throw new stubMethodNotSupportedException('The method ' . $method . ' is not supported by the current filter.');
    }
}
?>