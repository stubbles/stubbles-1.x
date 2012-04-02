<?php
/**
 * Class for changing values with a regular expression.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubRegexFilterDecorator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorFactory',
                      'net::stubbles::ipo::request::filter::stubStrategyFilterDecorator'
);
/**
 * Class for changing values with a regular expression.
 *
 * This filter does a check against a regular expression and returns the
 * requested match. If no match is found the returned value will be null.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubRegexFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * regex
     *
     * @var  string
     */
    protected $regex;

    /**
     * constructor
     *
     * @param  stubFilter  $filter    decorated filter
     * @param  string      $regex     regex to apply
     */
    function __construct(stubFilter $filter, $regex)
    {
        $this->setDecoratedFilter($filter);
        $this->regex = $regex;
    }

    /**
     * execute the filter
     *
     * @param   string  $value
     * @return  string
     * @throws  stubRuntimeException
     */
    protected function doExecute($value)
    {
        $matches = array();
        if (@preg_match($this->regex, $value, $matches) === false) {
            throw new stubRuntimeException('Invalid regular expression ' . $this->regex);
        }
        
        if (isset($matches[0]) === true) {
            return $matches[0];
        }
        
        return null;
    }
}
?>