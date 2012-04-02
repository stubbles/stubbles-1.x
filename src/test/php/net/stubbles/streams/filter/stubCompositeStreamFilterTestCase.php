<?php
/**
 * Test for net::stubbles::streams::filter::stubCompositeStreamFilter.
 *
 * @package     stubbles
 * @subpackage  streams_filter_test
 * @version     $Id: stubCompositeStreamFilterTestCase.php 2297 2009-08-21 15:22:25Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::filter::stubCompositeStreamFilter');
/**
 * Test for net::stubbles::streams::filter::stubCompositeStreamFilter.
 *
 * @package     stubbles
 * @subpackage  streams_filter_test
 * @group       streams
 * @group       streams_filter
 */
class stubCompositeStreamFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubCompositeStreamFilter
     */
    protected $compositeStreamFilter;
    /**
     * mocked stream filter
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStreamFilter1;
    /**
     * mocked stream filter
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStreamFilter2;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockStreamFilter1     = $this->getMock('stubStreamFilter');
        $this->mockStreamFilter2     = $this->getMock('stubStreamFilter');
        $this->compositeStreamFilter = new stubCompositeStreamFilter();
        $this->compositeStreamFilter->addStreamFilter($this->mockStreamFilter1)
                                    ->addStreamFilter($this->mockStreamFilter2);
    }

    /**
     * @test
     */
    public function returnsFalseIfNoFilterAdded()
    {
        $this->compositeStreamFilter = new stubCompositeStreamFilter();
        $this->assertFalse($this->compositeStreamFilter->shouldFilter('foo'));
    }

    /**
     * if no filter applies the composite returns false
     *
     * @test
     */
    public function noFilterAppliesReturnsFalse()
    {
        $this->mockStreamFilter1->expects($this->once())
                                ->method('shouldFilter')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(false));
        $this->mockStreamFilter2->expects($this->once())
                                ->method('shouldFilter')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(false));
        $this->assertFalse($this->compositeStreamFilter->shouldFilter('foo'));
    }

    /**
     * if no filter applies the composite returns false
     *
     * @test
     */
    public function filterAppliesReturnsTrue()
    {
        $this->mockStreamFilter1->expects($this->once())
                                ->method('shouldFilter')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(true));
        $this->mockStreamFilter2->expects($this->never())
                                ->method('shouldFilter');
        $this->assertTrue($this->compositeStreamFilter->shouldFilter('foo'));
    }
}
?>