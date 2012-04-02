<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubDefaultValueFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubDefaultValueFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubDefaultValueFilterDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubDefaultValueFilterDecorator extends stubDefaultValueFilterDecorator
{
    /**
     * helper method for direct access to protected doExecute()
     *
     * @param   mixed  $value
     * @return  mixed
     */
    public function callDoExecute($value)
    {
        return $this->doExecute($value);
    }
}
/**
 * Tests for net::stubbles::ipo::request::filter::stubDefaultValueFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubDefaultValueFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubDefaultValueFilterDecorator
     */
    protected $defaultValueFilterDecorator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultValueFilterDecorator = new TeststubDefaultValueFilterDecorator($this->getMock('stubFilter'), 'defaultValue');
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function defaultValue()
    {
        $this->assertEquals('defaultValue', $this->defaultValueFilterDecorator->getDefaultValue());
        $this->assertEquals('defaultValue', $this->defaultValueFilterDecorator->callDoExecute(null));
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function passThru()
    {
        $this->assertEquals('defaultValue', $this->defaultValueFilterDecorator->getDefaultValue());
        $this->assertEquals('other', $this->defaultValueFilterDecorator->callDoExecute('other'));
    }
}
?>