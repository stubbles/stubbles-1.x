<?php
/**
 * Test for net::stubbles::streams::stubPrefixedStreamFactory.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @version     $Id: stubPrefixedStreamFactoryTestCase.php 2299 2009-08-24 14:09:44Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubPrefixedStreamFactory');
/**
 * Test for net::stubbles::streams::stubPrefixedStreamFactory.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @group       streams
 */
class stubPrefixedStreamFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPrefixedStreamFactory
     */
    protected $prefixedStreamFactory;
    /**
     * mocked stream factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStreamFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockStreamFactory     = $this->getMock('stubStreamFactory');
        $this->prefixedStreamFactory = new stubPrefixedStreamFactory($this->mockStreamFactory, 'prefix/');
    }

    /**
     * @test
     */
    public function inputStreamGetsPrefix()
    {
        $mockInputStream = $this->getMock('stubInputStream');
        $this->mockStreamFactory->expects($this->once())
                                ->method('createInputStream')
                                ->with($this->equalTo('prefix/foo'), $this->equalTo(array('bar' => 'baz')))
                                ->will($this->returnValue($mockInputStream));
        $this->assertSame($mockInputStream, $this->prefixedStreamFactory->createInputStream('foo', array('bar' => 'baz')));
    }

    /**
     * @test
     */
    public function outputStreamGetsPrefix()
    {
        $mockOutputStream = $this->getMock('stubOutputStream');
        $this->mockStreamFactory->expects($this->once())
                                ->method('createOutputStream')
                                ->with($this->equalTo('prefix/foo'), $this->equalTo(array('bar' => 'baz')))
                                ->will($this->returnValue($mockOutputStream));
        $this->assertSame($mockOutputStream, $this->prefixedStreamFactory->createOutputStream('foo', array('bar' => 'baz')));
    }
}
?>