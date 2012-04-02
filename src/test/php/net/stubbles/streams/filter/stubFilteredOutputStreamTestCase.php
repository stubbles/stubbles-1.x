<?php
/**
 * Test for net::stubbles::streams::filter::stubFilteredOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_filter_test
 * @version     $Id: stubFilteredOutputStreamTestCase.php 2296 2009-08-20 22:17:45Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::filter::stubFilteredOutputStream');
/**
 * Test for net::stubbles::streams::filter::stubFilteredOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_filter_test
 * @group       streams
 * @group       streams_filter
 */
class stubFilteredOutputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubFilteredOutputStream
     */
    protected $filteredOutputStream;
    /**
     * mocked input stream
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockOutputStream;
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
        $this->mockOutputStream     = $this->getMock('stubOutputStream');
        $this->mockStreamFilter     = $this->getMock('stubStreamFilter');
        $this->filteredOutputStream = new stubFilteredOutputStream($this->mockOutputStream, $this->mockStreamFilter);
    }

    /**
     * data passing the filter should be written
     *
     * @test
     */
    public function dataPassingTheFilterShouldBeWritten()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('write')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(false));
        $this->assertEquals(3, $this->filteredOutputStream->write('foo'));
    }

    /**
     * data passing the filter should be written
     *
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWritten()
    {
        $this->mockOutputStream->expects($this->never())
                               ->method('write');
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(true));
        $this->assertEquals(0, $this->filteredOutputStream->write('foo'));
    }

    /**
     * data passing the filter should be written
     *
     * @test
     */
    public function dataPassingTheFilterShouldBeWrittenAsLine()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(false));
        $this->assertEquals(3, $this->filteredOutputStream->writeLine('foo'));
    }

    /**
     * data passing the filter should be written
     *
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWrittenAsLine()
    {
        $this->mockOutputStream->expects($this->never())
                               ->method('writeLine');
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(true));
        $this->assertEquals(0, $this->filteredOutputStream->writeLine('foo'));
    }
}
?>