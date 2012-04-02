<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubPasswordFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubPasswordFilterTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubPasswordFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubPasswordFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubPasswordFilterTestCase extends PHPUnit_Framework_TestCase
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
     * @var  stubPasswordFilter
     */
    protected $passwordFilter;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->mockRequestValueErrorFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->passwordFilter = new stubPasswordFilter($this->mockRequestValueErrorFactory);
        $this->passwordFilter->minDiffChars(null);
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function value()
    {
        $this->assertEquals('foo', $this->passwordFilter->execute('foo'));
        $this->assertEquals('425%$%"�$%t 32', $this->passwordFilter->execute('425%$%"�$%t 32'));
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function emptyValue()
    {
        $this->assertNull($this->passwordFilter->execute(''));
        $this->assertNull($this->passwordFilter->execute(null));
          
    }

    /**
     * assure that array values are returned the expected way
     *
     * @test
     */
    public function arrayValue()
    {
        $this->assertEquals('foo', $this->passwordFilter->execute(array('foo', 'foo')));
    }

    /**
     * assure that array values are returned the expected way
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function arrayValueFails()
    {
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('PASSWORDS_NOT_EQUAL'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->passwordFilter->execute(array('foo', 'bar'));
    }

    /**
     * assure that an unexpected value throws a stubFilterException
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function unexpectedValue()
    {
        $this->passwordFilter->nonAllowedValues(array('bar'));
        $this->assertEquals(array('bar'), $this->passwordFilter->getNonAllowedValues());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('PASSWORD_INVALID'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->passwordFilter->execute('bar');
    }

    /**
     * test checking for minimum amount of different characters works
     *
     * @test
     */
    public function minDiffChars()
    {
        $this->passwordFilter->minDiffChars(5);
        $this->assertEquals(5, $this->passwordFilter->getMinDiffChars());
        $this->assertEquals('abcde', $this->passwordFilter->execute(array('abcde', 'abcde')));
    }

    /**
     * test checking for minimum amount of different characters works
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function minDiffCharsFails()
    {
        $this->passwordFilter->minDiffChars(5);
        $this->assertEquals(5, $this->passwordFilter->getMinDiffChars());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('PASSWORD_TOO_LESS_DIFF_CHARS'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->passwordFilter->execute(array('abcdd', 'abcdd'));
    }
}
?>