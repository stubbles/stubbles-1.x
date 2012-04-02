<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubRangeFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubRangeFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubRangeFilterDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubRangeFilterDecorator extends stubRangeFilterDecorator
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
 * Tests for net::stubbles::ipo::request::filter::stubRangeFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubRangeFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
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
    protected $mockValidatorMin;
    /**
     * a mock to be used for the maximum validator
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockValidatorMax;
    /**
     * instance to test
     *
     * @var  TeststubRangeFilterDecorator
     */
    protected $rangeFilterDecorator;
    
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequestValueErrorFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->mockValidatorMin             = $this->getMock('stubValidator');
        $this->mockValidatorMax             = $this->getMock('stubValidator');
        $this->rangeFilterDecorator         = new TeststubRangeFilterDecorator($this->getMock('stubFilter'), $this->mockRequestValueErrorFactory);
    }

    /**
     * no validator does not do any harm
     *
     * @test
     */
    public function noValidatorReturnsValueAsIs()
    {
        $this->assertNull($this->rangeFilterDecorator->callDoExecute(null));
        $this->assertEquals('', $this->rangeFilterDecorator->callDoExecute(''));
        $this->assertEquals(true, $this->rangeFilterDecorator->callDoExecute(true));
        $this->assertEquals(false, $this->rangeFilterDecorator->callDoExecute(false));
        $this->assertEquals(313, $this->rangeFilterDecorator->callDoExecute(313));
    }

    /**
     * assure that an FilterException is thrown when value smaller then $min
     *
     * @test
     */
    public function withMinValidator()
    {
        $this->mockValidatorMin->expects($this->once())
                               ->method('validate')
                               ->will($this->returnValue(true));
        $this->mockValidatorMin->expects($this->never())
                               ->method('getCriteria');
        $this->rangeFilterDecorator->setMinValidator($this->mockValidatorMin);
        $this->assertSame($this->mockValidatorMin, $this->rangeFilterDecorator->getMinValidator());
        $this->assertEquals(-10, $this->rangeFilterDecorator->callDoExecute(-10));
    }

    /**
     * assure that an FilterException is thrown when value smaller then $min
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function withMinValidatorFails()
    {
        $this->mockValidatorMin->expects($this->once())
                               ->method('validate')
                               ->will($this->returnValue(false));
        $this->mockValidatorMin->expects($this->once())
                               ->method('getCriteria')
                               ->will($this->returnValue(array()));
        $this->rangeFilterDecorator->setMinValidator($this->mockValidatorMin);
        $this->assertEquals('VALUE_TOO_SMALL', $this->rangeFilterDecorator->getMinErrorId());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('VALUE_TOO_SMALL'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->rangeFilterDecorator->callDoExecute(-11);
    }

    /**
     * assure that an FilterException is thrown when value smaller then $min
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function withMinValidatorFailsDifferentErrorId()
    {
        $this->mockValidatorMin->expects($this->once())
                               ->method('validate')
                               ->will($this->returnValue(false));
        $this->mockValidatorMin->expects($this->once())
                               ->method('getCriteria')
                               ->will($this->returnValue(array()));
        $this->rangeFilterDecorator->setMinValidator($this->mockValidatorMin, 'differentErrorId');
        $this->assertEquals('differentErrorId', $this->rangeFilterDecorator->getMinErrorId());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('differentErrorId'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->rangeFilterDecorator->callDoExecute(-11);
    }

    /**
     * assure that an FilterException is thrown when value greater then $max
     *
     * @test
     */
    public function withMaxValidator()
    {
        $this->mockValidatorMax->expects($this->once())
                               ->method('validate')
                               ->will($this->returnValue(true));
        $this->mockValidatorMax->expects($this->never())
                               ->method('getCriteria');
        $this->rangeFilterDecorator->setMaxValidator($this->mockValidatorMax);
        $this->assertSame($this->mockValidatorMax, $this->rangeFilterDecorator->getMaxValidator());
        $this->assertEquals(10, $this->rangeFilterDecorator->callDoExecute(10));
    }

    /**
     * assure that an FilterException is thrown when value greater then $max
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function withMaxValidatorFails()
    {
        $this->mockValidatorMax->expects($this->once())
                               ->method('validate')
                               ->will($this->returnValue(false));
        $this->mockValidatorMax->expects($this->once())
                               ->method('getCriteria')
                               ->will($this->returnValue(array()));
        $this->rangeFilterDecorator->setMaxValidator($this->mockValidatorMax);
        $this->assertEquals('VALUE_TOO_GREAT', $this->rangeFilterDecorator->getMaxErrorId());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('VALUE_TOO_GREAT'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->rangeFilterDecorator->callDoExecute(11);
    }

    /**
     * assure that an FilterException is thrown when value greater then $max
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function withMaxValidatorFailsDifferentErrorId()
    {
        $this->mockValidatorMax->expects($this->once())
                               ->method('validate')
                               ->will($this->returnValue(false));
        $this->mockValidatorMax->expects($this->once())
                               ->method('getCriteria')
                               ->will($this->returnValue(array()));
        $this->rangeFilterDecorator->setMaxValidator($this->mockValidatorMax, 'differentErrorId');
        $this->assertEquals('differentErrorId', $this->rangeFilterDecorator->getMaxErrorId());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('differentErrorId'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->rangeFilterDecorator->callDoExecute(11);
    }
}
?>