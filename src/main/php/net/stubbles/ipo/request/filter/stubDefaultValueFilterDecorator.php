<?php
/**
 * Filter for returning a default value if given value is null.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubStrategyFilterDecorator');
/**
 * Filter for returning a default value if given value is null.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubDefaultValueFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * the default value in case given value not set
     *
     * @var  mixed
     */
    protected $defaultValue = null;

    /**
     * constructor
     *
     * @param  stubFilter  $filter        decorated filter
     * @param  mixed       $defaultValue  default value to return if filteres value is empty
     */
    public function __construct(stubFilter $filter, $defaultValue)
    {
        $this->setDecoratedFilter($filter);
        $this->defaultValue = $defaultValue;
    }

    /**
     * returns the default value
     *
     * @return  mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
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
        if (null === $value) {
            return $this->defaultValue;
        }
        
        return $value;
    }
}
?>