<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubHTTPURLFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubHTTPURLFilterTestCase.php 2330 2009-09-16 17:45:44Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubHTTPURLFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubHTTPURLFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubHTTPURLFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubHTTPURLFilter
     */
    protected $httpURLFilter;
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
        $this->httpURLFilter = new stubHTTPURLFilter($this->mockRequestValueErrorFactory);
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function value()
    {
        $this->assertEquals('http://example.org/', $this->httpURLFilter->execute('http://example.org'));
        $this->assertEquals('http://example.org:45/', $this->httpURLFilter->execute('http://example.org:45'));
    }

    /**
     * assure correct behaviour when a null value is passed
     *
     * @test
     */
    public function nullValue()
    {
        $this->assertNull($this->httpURLFilter->execute(null));
    }

    /**
     * assure correct behaviour when an empty value is passed
     *
     * @test
     */
    public function emptyValue()
    {
        $this->assertNull($this->httpURLFilter->execute(''));
    }

    /**
     * assure that an exception is thrown when a wrong scheme is passed
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function wrongScheme()
    {
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('URL_INCORRECT'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->httpURLFilter->execute('ftp://foobar.de/');
    }

    /**
     * assure that an exception is thrown when a wrong value is passed
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function wrongValue()
    {
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('URL_INCORRECT'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->httpURLFilter->execute('http://wrong example!');
    }

    /**
     * assert that a non-existing but correct URL is treated correct
     *
     * @test
     */
    public function urlCheckDisabled()
    {
        $this->assertFalse($this->httpURLFilter->isDNSCheckEnabled());
        $this->assertEquals('http://doesnotexist.1und1.de/', $this->httpURLFilter->execute('http://doesnotexist.1und1.de/'));
        $this->assertSame($this->httpURLFilter, $this->httpURLFilter->setCheckDNS(true));
    }

    /**
     * assert that a non-existing but correct URL is treated correct
     *
     * @test
     */
    public function urlNotAvailable()
    {
        $this->httpURLFilter->setCheckDNS(true);
        $this->assertTrue($this->httpURLFilter->isDNSCheckEnabled());
        if (DIRECTORY_SEPARATOR === '\\' && version_compare(PHP_VERSION, '5.3', '<') === true) {
            // Windows does not support dns checks, filter will always return ok
            $this->assertEquals('http://doesnotexist.1und1.de/', $this->httpURLFilter->execute('http://doesnotexist.1und1.de/'));
        } else {
            $this->setExpectedException('stubFilterException');
            $this->mockRequestValueErrorFactory->expects($this->once())
                                               ->method('create')
                                               ->with($this->equalTo('URL_NOT_AVAILABLE'))
                                               ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
            $this->httpURLFilter->execute('http://doesnotexist.1und1.de/');
        }
    }
}
?>