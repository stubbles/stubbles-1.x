<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubRegexFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubRegexFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubRegexFilterDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubRegexFilterDecorator extends stubRegexFilterDecorator
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
 * Tests for net::stubbles::ipo::request::filter::stubRegexFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubRegexFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test with 'before' decorator strategy
     *
     * @var  stubRegexFilterDecorator
     */
    protected $regexFilterDecorator;
    /**
     * regex for testing purpose
     *
     * @var  string
     */
    protected $regex                      = '/start_middle(?=_end)/';

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->regexFilterDecorator = new TeststubRegexFilterDecorator($this->getMock('stubFilter'), $this->regex);
    }

    /**
     * assure ignoring null values
     *
     * @test
     */
    public function withNull()
    {
        $this->assertNull($this->regexFilterDecorator->callDoExecute(null));
    }

    /**
     * assure ignoring empty values
     *
     * @test
     */
    public function withEmptyString() 
    {
        $this->assertEquals('', $this->regexFilterDecorator->callDoExecute(''));
    }

    /**
     * assure regex functionality  with matching & nonmatching values
     *
     * @test
     */
    public function decoratorRegex() 
    {
        $result = $this->regexFilterDecorator->callDoExecute('start_middle_end');
        $this->assertEquals('start_middle', $result);
        
        $result = $this->regexFilterDecorator->callDoExecute('start_middle');
        $this->assertNull($result);
    }

    /**
     * invalid regular expression cause runtime exception
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function decoratorRegexFails() 
    {
        $invalidRegexDecorator = new TeststubRegexFilterDecorator($this->getMock('stubFilter'), 'noRegexSlashes');
        $invalidRegexDecorator->callDoExecute('irrelevant for this test');
    }
}
?>