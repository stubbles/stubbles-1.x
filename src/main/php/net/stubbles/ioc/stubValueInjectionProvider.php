<?php
/**
 * Simple injection provider for a single predefined value.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubValueInjectionProvider.php 2060 2009-01-26 12:57:25Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider');
/**
 * Simple injection provider for a single predefined value.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubValueInjectionProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * value to provide
     *
     * @var  mixed
     */
    protected $value;

    /**
     * constructor
     *
     * @param  mixed  $value  value to provide
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        return $this->value;
    }
}
?>