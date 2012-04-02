<?php
/**
 * Tests for net::stubbles::ipo::request::stubAbstractRequest.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubAbstractRequestTestCase.php 2680 2010-08-23 22:02:52Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubAbstractRequest',
                      'net::stubbles::ipo::request::filter::stubFilter'
);
class stubTestExceptionFilter extends stubBaseObject implements stubFilter
{
    public function execute($value)
    {
        throw new stubFilterException(new stubRequestValueError('foo', array()));
    }
}
class stubTestRequest extends stubAbstractRequest
{
    protected $_rawData = 'This is the raw request data.';
    
    protected function doConstuct()
    {
        $this->unsecureParams  = array('foo' => 'bar');
        $this->unsecureHeaders = array('bar' => 'baz');
        $this->unsecureCookies = array('baz' => 'foo');
    }

    public function removeCookieValues()
    {
        $this->unsecureCookies = array();
    }

    public function getMethod()
    {
        return 'test';
    }
    
    public function getURI()
    {
        return 'test://' . __FILE__;
    }

    public function getCompleteUri()
    {
        return $this->getURI();
    }
    
    public function setRawData($rawData)
    {
        $this->_rawData = $rawData;
    }
    
    protected function getRawData()
    {
        return $this->_rawData;
    }
}
/**
 * Tests for net::stubbles::ipo::request::stubAbstractRequest.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @group       ipo
 * @group       ipo_request
 */
class stubAbstractRequestTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubTestRequest
     */
    protected $request;
    /**
     * mocked filter factory
     *
     * @var    PHPUnit_Framework_MockObject_MockObject
     * @since  1.3.0
     */
    protected $mockFilterFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockFilterFactory   = $this->getMock('stubFilterFactory');
        $this->request             = new stubTestRequest($this->mockFilterFactory);
    }

    /**
     * test whether cookies are accepted or not
     *
     * @test
     */
    public function acceptsCookies()
    {
        $this->assertTrue($this->request->acceptsCookies());
        $this->request->removeCookieValues();
        $this->assertFalse($this->request->acceptsCookies());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasParamReturnsTrueIfParamSet()
    {
        $this->assertTrue($this->request->hasParam('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasParamReturnsFalseIfParamNotSet()
    {
        $this->assertFalse($this->request->hasParam('baz'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasHeaderReturnsTrueIfHeaderSet()
    {
        $this->assertTrue($this->request->hasHeader('bar'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasHeaderReturnsFalseIfHeaderNotSet()
    {
        $this->assertFalse($this->request->hasHeader('baz'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasCookieReturnsTrueIfCookieSet()
    {
        $this->assertTrue($this->request->hasCookie('baz'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function hasCookieReturnsFalseIfCookieNotSet()
    {
        $this->assertFalse($this->request->hasCookie('foo'));
    }

    /**
     * assure that cancelling the request works as expected
     *
     * @test
     */
    public function cancelRequest()
    {
        $this->assertFalse($this->request->isCancelled());
        $this->request->cancel();
        $this->assertTrue($this->request->isCancelled());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function paramErrorsReturnsAlwaysSameInstance()
    {
        $this->assertSame($this->request->paramErrors(),
                          $this->request->paramErrors()
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function headerErrorsReturnsAlwaysSameInstance()
    {
        $this->assertSame($this->request->headerErrors(),
                          $this->request->headerErrors()
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function cookieErrorsReturnsAlwaysSameInstance()
    {
        $this->assertSame($this->request->cookieErrors(),
                          $this->request->cookieErrors()
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function bodyErrorsReturnsAlwaysSameInstance()
    {
        $this->assertSame($this->request->bodyErrors(),
                          $this->request->bodyErrors()
        );
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateParamReturnsValidatingRequestValue()
    {
        $this->assertTrue($this->request->validateParam('foo')->isEqualTo('bar'));

    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateHeaderReturnsValidatingRequestValue()
    {
        $this->assertTrue($this->request->validateHeader('bar')->isEqualTo('baz'));

    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateCookieReturnsValidatingRequestValue()
    {
        $this->assertTrue($this->request->validateCookie('baz')->isEqualTo('foo'));

    }

    /**
     * @test
     * @since  1.3.0
     */
    public function validateBodyReturnsFilteringRequestValue()
    {
        $this->request->setRawData('bar');
        $this->assertTrue($this->request->validateBody()->isEqualTo('bar'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readParamReturnsFilteringRequestValue()
    {
        $this->assertEquals('bar', $this->request->readParam('foo')->unsecure());

    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readHeaderReturnsFilteringRequestValue()
    {
        $this->assertEquals('baz', $this->request->readHeader('bar')->unsecure());

    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readCookieReturnsFilteringRequestValue()
    {
        $this->assertEquals('foo', $this->request->readCookie('baz')->unsecure());

    }

    /**
     * @test
     * @since  1.3.0
     */
    public function readBodyReturnsFilteringRequestValue()
    {
        $this->request->setRawData('bar');
        $this->assertEquals('bar', $this->request->readBody()->unsecure());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getParamNamesReturnsListOfParameterNames()
    {
        $this->assertEquals(array('foo'), $this->request->getParamNames());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getHeaderNamesReturnsListOfHeaderNames()
    {
        $this->assertEquals(array('bar'), $this->request->getHeaderNames());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getCookieNamesReturnsListOfCookieNames()
    {
        $this->assertEquals(array('baz'), $this->request->getCookieNames());
    }

    /**
     * @test
     * @expectedException  stubRuntimeException
     */
    public function cloningRequestThrowsRuntimeException()
    {
        $request = clone $this->request;
    }
}
?>