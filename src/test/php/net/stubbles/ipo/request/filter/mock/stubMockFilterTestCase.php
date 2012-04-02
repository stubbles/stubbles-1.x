<?php
/**
 * Tests for net::stubbles::ipo::request::filter::mock::stubMockFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_mock_test
 * @version     $Id: stubMockFilterTestCase.php 2647 2010-08-18 12:28:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::mock::stubMockFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::mock::stubMockFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_mock_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_mock
 */
class stubMockFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMockFilter
     */
    protected $mockFilter;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->mockFilter = new stubMockFilter();
    }

    /**
     * @test
     */
    public function executeReturnsGivenValue()
    {
        $this->assertEquals('foo', $this->mockFilter->execute('foo'));
    }

    /**
     * @test
     */
    public function anyOtherMethodCallReturnsInstance()
    {
        $this->assertSame($this->mockFilter, $this->mockFilter->doesNotExist());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function anyOtherMethodCallIsRecorded()
    {
        $this->assertFalse($this->mockFilter->wasMethodCalled('doesNotExist'));
        $this->mockFilter->doesNotExist();
        $this->assertTrue($this->mockFilter->wasMethodCalled('doesNotExist'));
    }
}
?>