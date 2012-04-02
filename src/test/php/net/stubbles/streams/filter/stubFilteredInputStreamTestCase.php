<?php
/**
 * Test for net::stubbles::streams::filter::stubFilteredInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_filter_test
 * @version     $Id: stubFilteredInputStreamTestCase.php 2296 2009-08-20 22:17:45Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::filter::stubFilteredInputStream');
/**
 * Test for net::stubbles::streams::filter::stubFilteredInputStream.
 *
 * @package     stubbles
 * @subpackage  streams_filter_test
 * @group       streams
 * @group       streams_filter
 */
class stubFilteredInputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubFilteredInputStream
     */
    protected $filteredInputStream;
    /**
     * mocked input stream
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInputStream;
    /**
     * mocked stream filter
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStreamFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInputStream     = $this->getMock('stubInputStream');
        $this->mockStreamFilter    = $this->getMock('stubStreamFilter');
        $this->filteredInputStream = new stubFilteredInputStream($this->mockInputStream, $this->mockStreamFilter);
    }

    /**
     * data returned from read() should be filtered
     *
     * @test
     */
    public function readAndFilter()
    {
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, false));
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('read')
                              ->with($this->equalTo(8192))
                              ->will($this->onConsecutiveCalls('foo', 'bar'));
        $this->mockStreamFilter->expects($this->exactly(2))
                               ->method('shouldFilter')
                               ->will($this->onConsecutiveCalls(true, false));
        $this->assertEquals('bar', $this->filteredInputStream->read());
    }

    /**
     * data returned from read() should be filtered
     *
     * @test
     */
    public function readAndFilterUntilEnd()
    {
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, true));
        $this->mockInputStream->expects($this->once())
                              ->method('read')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue('foo'));
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(true));
        $this->assertEquals('', $this->filteredInputStream->read());
    }

    /**
     * data returned from readLine() should be filtered
     *
     * @test
     */
    public function readLineAndFilter()
    {
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, false));
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('readLine')
                              ->with($this->equalTo(8192))
                              ->will($this->onConsecutiveCalls('foo', 'bar'));
        $this->mockStreamFilter->expects($this->exactly(2))
                               ->method('shouldFilter')
                               ->will($this->onConsecutiveCalls(true, false));
        $this->assertEquals('bar', $this->filteredInputStream->readLine());
    }

    /**
     * data returned from readLine() should be filtered
     *
     * @test
     */
    public function readLineAndFilterUntilEnd()
    {
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, true));
        $this->mockInputStream->expects($this->once())
                              ->method('readLine')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue('foo'));
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(true));
        $this->assertEquals('', $this->filteredInputStream->readLine());
    }
}
?>