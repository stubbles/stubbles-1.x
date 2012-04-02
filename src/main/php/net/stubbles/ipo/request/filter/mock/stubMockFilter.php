<?php
/**
 * Mocked filter to be used in unit tests.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_filter_mock
 * @version     $Id: stubMockFilter.php 2647 2010-08-18 12:28:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilterException');
/**
 * Mocked filter to be used in unit tests.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_mock
 */
class stubMockFilter extends stubBaseObject implements stubFilter
{
    /**
     * list of called methods
     *
     * @var    array<string,string>
     * @since  1.3.0
     */
    protected $calledMethods = array();

    /**
     * execute the filter
     *
     * @param   mixed                $value  value to filter
     * @return  mixed                filtered value
     * @throws  stubFilterException  in case $value has errors
     */
    public function execute($value)
    {
        return $value;
    }

    /**
     * pass thru any method call
     *
     * @param   string          $method
     * @param   array           $arguments
     * @return  stubMockFilter
     */
    public function __call($method, $arguments)
    {
        $this->calledMethods[$method] = $method;
        return $this;
    }

    /**
     *
     * @param   string  $methodName
     * @return  bool
     * @since   1.3.0
     */
    public function wasMethodCalled($methodName)
    {
        return isset($this->calledMethods[$methodName]);
    }
}
?>