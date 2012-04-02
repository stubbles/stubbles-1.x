<?php
/**
 * Tests for net::stubbles::ipo::request::filter::mock::stubMockFilterFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_mock_test
 * @version     $Id: stubMockFilterFactoryTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::mock::stubMockFilterFactory');
/**
 * Tests for net::stubbles::ipo::request::filter::mock::stubMockFilterFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_mock_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_mock
 */
class stubMockFilterFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMockFilterFactory
     */
    protected $mockFilterFactory;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->mockFilterFactory = new stubMockFilterFactory();
    }

    /**
     * @test
     */
    public function createForTypeAlwaysReturnsMockFilter()
    {
        $this->assertInstanceOf('stubMockFilter', $this->mockFilterFactory->createForType('foo'));
    }

    /**
     * @test
     */
    public function createBuilderAlwaysReturnsMockFilter()
    {
        $this->assertInstanceOf('stubMockFilter', $this->mockFilterFactory->createBuilder($this->getMock('stubFilter')));
    }
}
?>