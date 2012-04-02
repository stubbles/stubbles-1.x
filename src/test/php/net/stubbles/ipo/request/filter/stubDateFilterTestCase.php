<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubDateFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubDateFilterTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubDateFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubDateFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubDateFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDateFilter
     */
    protected $dateFilter;
    /**
     * a mock to be used for the rveFactory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequestValueErrorFactory;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->mockRequestValueErrorFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->dateFilter                   = new stubDateFilter($this->mockRequestValueErrorFactory);
    }

    /**
     * assure that empty values are returned as null
     *
     * @test
     */
    public function emptyValuesAreReturnedAsNull()
    {
        $this->mockRequestValueErrorFactory->expects($this->never())
                                           ->method('create');
        $this->assertNull($this->dateFilter->execute(''));
        $this->assertNull($this->dateFilter->execute(null));
        $this->assertNull($this->dateFilter->execute(0));
    }

    /**
     * valid dates should be returned as date instance
     *
     * @test
     */
    public function validDatesAreReturnedAsDateInstance()
    {
        $this->mockRequestValueErrorFactory->expects($this->never())
                                           ->method('create');
        $date = $this->dateFilter->execute('2008-09-27');
        $this->assertInstanceOf('stubDate', $date);
        $this->assertEquals(2008, $date->getYear());
        $this->assertEquals(9, $date->getMonth());
        $this->assertEquals(27, $date->getDay());
        $this->assertEquals(0, $date->getHours());
        $this->assertEquals(0, $date->getMinutes());
        $this->assertEquals(0, $date->getSeconds());
    }

    /**
     * invalid dates throw a filter exception
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function invalidDatesThrowFilterException()
    {
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('DATE_INVALID'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $date = $this->dateFilter->execute('invalid date');
    }
}
?>