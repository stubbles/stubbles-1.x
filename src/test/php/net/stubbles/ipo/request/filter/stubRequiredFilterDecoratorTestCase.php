<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubRequiredFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubRequiredFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubRequiredFilterDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubRequiredFilterDecorator extends stubRequiredFilterDecorator
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
 * Tests for net::stubbles::ipo::request::filter::stubRequiredFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubRequiredFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * a mock to be used for the rveFactory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequestValueErrorFactory;
    /**
     * the instance to test
     *
     * @var  TeststubRequiredFilterDecorator
     */
    protected $requiredFilterDecorator;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockRequestValueErrorFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->requiredFilterDecorator        = new TeststubRequiredFilterDecorator($this->getMock('stubFilter'), $this->mockRequestValueErrorFactory);
    }

    /**
     * required means null is not allowed
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function nullFailsDefault()
    {
        $this->assertEquals('FIELD_EMPTY', $this->requiredFilterDecorator->getErrorId());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('FIELD_EMPTY'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->requiredFilterDecorator->callDoExecute(null);
    }

    /**
     * required means null is not allowed
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function nullFailsDifferentErrorId()
    {
        $this->requiredFilterDecorator->setErrorId('test');
        $this->assertEquals('test', $this->requiredFilterDecorator->getErrorId());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('test'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->requiredFilterDecorator->callDoExecute(null);
    }

    /**
     * required means empty string is not allowed
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function emptyFailsDefault()
    {
        $this->assertEquals('FIELD_EMPTY', $this->requiredFilterDecorator->getErrorId());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('FIELD_EMPTY'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->requiredFilterDecorator->callDoExecute('');
    }

    /**
     * required means empty string is not allowed
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function emptyFailsDifferentErrorId()
    {
        $this->requiredFilterDecorator->setErrorId('test');
        $this->requiredFilterDecorator->setErrorId('test');
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('test'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->requiredFilterDecorator->callDoExecute('');
    }

    /**
     * other values should pass
     *
     * @test
     */
    public function otherValues()
    {
        $this->mockRequestValueErrorFactory->expects($this->never())->method('create');
        $this->assertEquals('passes', $this->requiredFilterDecorator->callDoExecute('passes'));
    }
}
?>