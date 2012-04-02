<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubEncodingFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubEncodingFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubEncodingFilterDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubEncodingFilterDecorator extends stubEncodingFilterDecorator
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
 * Tests for net::stubbles::ipo::request::filter::stubEncodingFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubEncodingFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * a mock to be used for the minimum validator
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStringEncoder;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockStringEncoder = $this->getMock('stubStringEncoder');
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function encode()
    {
        $encodingFilterDecorator = new TeststubEncodingFilterDecorator($this->getMock('stubFilter'),
                                                                       $this->mockStringEncoder,
                                                                       stubStringEncoder::MODE_ENCODE
                                       );
        $this->assertSame($this->mockStringEncoder, $encodingFilterDecorator->getEncoder());
        $this->assertEquals(stubStringEncoder::MODE_ENCODE, $encodingFilterDecorator->getEncoderMode());
        $this->mockStringEncoder->expects($this->once())
                                ->method('apply')
                                ->with($this->equalTo('foo'), $this->equalTo(stubStringEncoder::MODE_ENCODE))
                                ->will($this->returnValue('encoded'));
        $this->assertEquals('encoded', $encodingFilterDecorator->callDoExecute('foo'));
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function decode()
    {
        $encodingFilterDecorator = new TeststubEncodingFilterDecorator($this->getMock('stubFilter'),
                                                                       $this->mockStringEncoder
                                       );
        $this->assertSame($this->mockStringEncoder, $encodingFilterDecorator->getEncoder());
        $this->assertEquals(stubStringEncoder::MODE_DECODE, $encodingFilterDecorator->getEncoderMode());
        $this->mockStringEncoder->expects($this->once())
                                ->method('apply')
                                ->with($this->equalTo('foo'), $this->equalTo(stubStringEncoder::MODE_DECODE))
                                ->will($this->returnValue('decoded'));
        $this->assertEquals('decoded', $encodingFilterDecorator->callDoExecute('foo'));
    }
}
?>