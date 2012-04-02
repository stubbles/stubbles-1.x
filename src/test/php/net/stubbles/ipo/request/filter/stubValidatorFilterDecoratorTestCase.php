<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubValidatorFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubValidatorFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubValidatorFilterDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubValidatorFilterDecorator extends stubValidatorFilterDecorator
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
 * Tests for net::stubbles::ipo::request::filter::stubValidatorFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubValidatorFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * a mock to be used for the rveFactory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequestValueErrorFactory;
    /**
     * a mock to be used for the minimum validator
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockValidator;
    /**
     * the instance to test
     *
     * @var  TeststubValidatorFilterDecorator
     */
    protected $validatorFilterDecorator;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockRequestValueErrorFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->mockValidator                = $this->getMock('stubValidator');
        $this->mockFilter                   = $this->getMock('stubFilter');
        $this->validatorFilterDecorator     = new TeststubValidatorFilterDecorator($this->mockFilter, $this->mockRequestValueErrorFactory, $this->mockValidator);
    }

    /**
     * test some values
     *
     * @test
     */
    public function values()
    {
        $this->assertSame($this->mockFilter, $this->validatorFilterDecorator->getDecoratedFilter());
        $this->assertSame($this->mockValidator, $this->validatorFilterDecorator->getValidator());
    }

    /**
     * assure that filtering a string with regular expressions works correct
     *
     * @test
     */
    public function validationSucceeds()
    {
        
        $this->mockValidator->expects($this->once())->method('validate')->will($this->returnValue(true));
        $this->mockRequestValueErrorFactory->expects($this->never())->method('create');
        $this->assertEquals('test_value', $this->validatorFilterDecorator->callDoExecute('test_value'));
    }

    /**
     * assure that filtering a string with regular expressions works correct
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function validationFails()
    {
        $this->assertEquals('FIELD_WRONG_VALUE', $this->validatorFilterDecorator->getErrorId());
        $this->mockValidator->expects($this->once())->method('validate')->will($this->returnValue(false));
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('FIELD_WRONG_VALUE'))
                                           ->will($this->returnValue(new stubRequestValueError('FIELD_WRONG_VALUE', array('en_EN' => 'Something wrent wrong.'))));
        $this->validatorFilterDecorator->callDoExecute('test_value');
    }

    /**
     * assure that filtering a string with regular expressions works correct
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function validationFailsWithOtherErrorId()
    {
        $this->validatorFilterDecorator->setErrorId('foo');
        $this->assertEquals('foo', $this->validatorFilterDecorator->getErrorId());
        $this->mockValidator->expects($this->once())->method('validate')->will($this->returnValue(false));
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('foo'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->validatorFilterDecorator->callDoExecute('test_value');
    }

    /**
     * test handling of empty values
     *
     * @test
     */
    public function emptyValues()
    {
        $this->mockValidator->expects($this->never())
                            ->method('validate');
        $this->assertNull($this->validatorFilterDecorator->callDoExecute(null));
        $this->assertNull($this->validatorFilterDecorator->callDoExecute(''));
    }

    /**
     * test handling of non-empty values
     *
     * @test
     */
    public function nonEmptyValues()
    {
        $this->mockValidator->expects($this->exactly(2))
                               ->method('validate')
                               ->will($this->returnValue(true));
        $this->assertEquals(0, $this->validatorFilterDecorator->callDoExecute(0));
        $this->assertEquals('0', $this->validatorFilterDecorator->callDoExecute('0'));
    }

}
?>