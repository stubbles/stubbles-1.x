<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubAbstractFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubAbstractFilterDecoratorTestCase.php 2327 2009-09-16 14:27:22Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubAbstractFilterDecorator',
                      'net::stubbles::ipo::request::filter::stubRegexFilterDecorator',
                      'net::stubbles::ipo::request::filter::stubTextFilter'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubAbstractFilterDecorator extends stubAbstractFilterDecorator
{
    // intentionally empty
}
/**
 * Tests for net::stubbles::ipo::request::filter::stubStrategyFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubAbstractFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * a mock to be used for the minimum validator
     *
     * @var  stubAbstractFilterDecorator
     */
    protected $abstractFilterDecorator;
    /**
     * mocked filter instance
     *
     * @var  stubFilter
     */
    protected $mockFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockFilter              = $this->getMock('stubFilter');
        $this->abstractFilterDecorator = new TeststubAbstractFilterDecorator();
        $this->abstractFilterDecorator->setDecoratedFilter($this->mockFilter);
    }

    /**
     * assert that decorated filter is same as in constructor
     *
     * @test
     */
    public function decoratedFilter()
    {
        $this->assertSame($this->mockFilter, $this->abstractFilterDecorator->getDecoratedFilter());
    }

    /**
     * @test
     */
    public function executeCallsExecuteOnDecoratedFilter()
    {
        $this->mockFilter->expects($this->once())
                         ->method('execute')
                         ->with($this->equalTo('value'))
                         ->will($this->returnValue('foo'));
        $this->assertSame('foo', $this->abstractFilterDecorator->execute('value'));
    }

    /**
     * test that string filter is created
     *
     * @test
     */
    public function callMethodOnDeeplyDecoratedFilter()
    {
        $textFilter = new stubTextFilter();
        $textFilter->setAllowedTags(array('b'));
        $filter = new stubRegexFilterDecorator($textFilter, '~foo~');
        $this->abstractFilterDecorator->setDecoratedFilter($filter);
        $this->assertEquals(array('b'), $this->abstractFilterDecorator->getAllowedTags());
    }

    /**
     * @test
     * @expectedException  stubMethodNotSupportedException
     */
    public function callNonExistingMethodThrowsException()
    {
        $this->abstractFilterDecorator->doesNotExist();
    }
}
?>