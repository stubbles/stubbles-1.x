<?php
/**
 * Test classes for net::stubbles::ipo::request::broker::stubRequestBroker.
 *
 * @package     stubbles
 * @subpackage  ipo_request_broker_test
 * @version     $Id: TestBrokerClasses.php 2630 2010-08-13 18:03:27Z mikey $
 */
/**
 * test class
 *
 * @package     stubbles
 * @subpackage  ipo_request_broker_test
 */
class TestBrokerClass
{
    /**
     * test property
     *
     * @var  string
     * @Filter[StringFilter](fieldName='foo', regex='~.*~')
     */
    public $foo         = null;
    /**
     * test property
     *
     * @var  string
     * @Filter[StringFilter](fieldName='dummy', regex='~.*~')
     */
    public static $dummy = null;
    /**
     * test property
     *
     * @var  string
     * @Filter[StringFilter](fieldName='bar', regex='~.*~')
     */
    protected $bar      = null;
    /**
     * test property
     *
     * @var  string
     * @Filter[StringFilter](fieldName='baz', regex='~.*~')
     */
    protected $baz      = null;

    /**
     * test method
     *
     * @var  string  $bar
     * @Filter[StringFilter](fieldName='dummy', regex='~.*~')
     */
    public static function setDummy($dummy)
    {
        self::$dummy = $dummy;
    }

    /**
     * test method
     *
     * @var  string  $bar
     * @Filter[StringFilter](fieldName='bar', regex='~.*~')
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    /**
     * test method
     *
     * @return  string
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * test method
     *
     * @var  string  $baz
     * @Filter[StringFilter](fieldName='baz', regex='~.*~')
     */
    protected function setBaz($baz)
    {
        $this->baz = $baz;
    }

    /**
     * test method
     *
     * @return  string
     */
    public function getBaz()
    {
        return $this->baz;
    }
}
/**
 * test class of instance stubObject
 *
 * @package     stubbles
 * @subpackage  ipo_request_broker_test
 */
class TestBrokerObject extends stubBaseObject
{
    /**
     * test property
     *
     * @var  string
     * @Filter[StringFilter](fieldName='foo', regex='~.*~')
     */
    public $foo    = null;
    /**
     * test property
     *
     * @var  string
     * @Filter[StringFilter](fieldName='dummy', regex='~.*~')
     */
    public static $dummy = null;
    /**
     * test property
     *
     * @var  string
     * @Filter[StringFilter](fieldName='bar', regex='~.*~')
     */
    protected $bar = null;
    /**
     * test property
     *
     * @var  string
     * @Filter[StringFilter](fieldName='baz', regex='~.*~')
     */
    protected $baz = null;

    /**
     * test method
     *
     * @var  string  $bar
     * @Filter[StringFilter](fieldName='dummy', regex='~.*~')
     */
    public static function setDummy($dummy)
    {
        self::$dummy = $dummy;
    }

    /**
     * test method
     *
     * @var  string  $bar
     * @Filter[StringFilter](fieldName='bar', regex='~.*~')
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    /**
     * test method
     *
     * @return  string
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * test method
     *
     * @var  string  $baz
     * @Filter[StringFilter](fieldName='baz', regex='~.*~')
     */
    protected function setBaz($baz)
    {
        $this->baz = $baz;
    }

    /**
     * test method
     *
     * @return  string
     */
    public function getBaz()
    {
        return $this->baz;
    }
}
?>