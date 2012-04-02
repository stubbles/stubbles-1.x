<?php
/**
 * Tests for net::stubbles::ipo::interceptors::stubETagPostInterceptor.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors_test
 * @version     $Id: stubETagPostInterceptorTestCase.php 3212 2011-11-10 21:20:11Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubETagPostInterceptor');
/**
 * Tests for net::stubbles::ipo::interceptors::stubETagPostInterceptor.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors_test
 * @group       ipo
 * @group       ipo_interceptors
 */
class stubETagPostInterceptorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubETagPostInterceptor
     */
    protected $eTagPostInterceptor;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked session instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
    /**
     * mocked response instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * ETag (e.g. '"h2j3f12jhf33fd89sdf900du3f12"');
     *
     * @var string
     */
    protected $ETag;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->eTagPostInterceptor   = new stubETagPostInterceptor();
        $this->mockRequest           = $this->getMock('stubRequest');
        $this->mockSession           = $this->getMock('stubSession');
        $this->mockResponse          = $this->getMock('stubResponse');
        $this->ETag                  = '"'.md5(serialize('my page content')).'"';

        
    }

    /**
     * returns list of valid status codes
     *
     * @return  array<string<array<int>>
     * @since   1.7.0
     */
    public function getValidStatusCodes()
    {
        return array('200' => array(200),
                     '203' => array(203),
                     '206' => array(206),
                     '300' => array(300),
                     '301' => array(301),
                     '410' => array(410)
        );
    }

    /**
     * @param  int  $statusCode
     * @since  1.7.0
     * @test
     * @dataProvider  getValidStatusCodes
     */
    public function doesAddEtagForStatusCode($statusCode)
    {
        $validatingRequestValue = new stubValidatingRequestValue('HTTP_IF_NONE_MATCH',
                                                                 $this->ETag
                                  );
        $this->mockRequest->expects($this->once())
                          ->method('validateHeader')
                          ->will($this->returnValue($validatingRequestValue));
        $this->mockResponse->expects($this->once())
                           ->method('getStatusCode')
                           ->will($this->returnValue($statusCode));
        $this->mockResponse->expects($this->once())
                           ->method('getBody')
                           ->will($this->returnValue('my page content'));
        $this->mockResponse->expects($this->atLeastOnce())
                           ->method('addHeader');
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo('304'))
                           ->will($this->returnValue($this->mockResponse));
        $this->eTagPostInterceptor->postProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
    }

    /**
     * returns list of valid status codes
     *
     * @return  array<string<array<int>>
     * @since   1.7.0
     */
    public function getInvalidStatusCodes()
    {
        return array('201' => array(201),
                     '202' => array(202),
                     '205' => array(205),
                     '302' => array(302),
                     '303' => array(303),
                     '404' => array(404),
                     '503' => array(503)
        );
    }

    /**
     * @param  int  $statusCode
     * @since  1.7.0
     * @test
     * @dataProvider  getInvalidStatusCodes
     */
    public function doesNotAddEtagForStatusCode($statusCode)
    {
        $this->mockRequest->expects($this->never())
                          ->method('validateHeader');
        $this->mockResponse->expects($this->once())
                           ->method('getStatusCode')
                           ->will($this->returnValue($statusCode));
        $this->mockResponse->expects($this->never())
                           ->method('addHeader');
        $this->mockResponse->expects($this->never())
                           ->method('setStatusCode');
        $this->eTagPostInterceptor->postProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
    }

    /**
     * @test
     */
    public function postProcessWithoutIfNoneMatchHeader()
    {
        $validatingRequestValue = new stubValidatingRequestValue('HTTP_IF_NONE_MATCH',
                                                                 'invalid'
                                  );
        $this->mockRequest->expects($this->once())
                          ->method('validateHeader')
                          ->will($this->returnValue($validatingRequestValue));
        $this->mockResponse->expects($this->at(0))
                           ->method('getStatusCode')
                           ->will($this->returnValue(200));
        $this->mockResponse->expects($this->once())
                           ->method('getBody')
                           ->will($this->returnValue('my page content'));
        $this->mockResponse->expects($this->at(2))
                           ->method('addHeader')
                           ->with($this->equalTo('ETag'), $this->equalTo($this->ETag));
        $this->mockResponse->expects($this->at(3))
                           ->method('addHeader')
                           ->with($this->equalTo('Cache-Control'), $this->equalTo('private'));
        $this->mockResponse->expects($this->at(4))
                           ->method('addHeader')
                           ->with($this->equalTo('Pragma'), $this->equalTo(''));
        $this->mockResponse->expects($this->never())
                           ->method('clearBody');

        $this->eTagPostInterceptor->postProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
    }

    /**
     * @test
     */
    public function postProcessWithIfNoneMatchHeader()
    {
        $validatingRequestValue = new stubValidatingRequestValue('HTTP_IF_NONE_MATCH',
                                                                 $this->ETag
                                  );
        $this->mockRequest->expects($this->once())
                          ->method('validateHeader')
                          ->will($this->returnValue($validatingRequestValue));

        $this->mockResponse->expects($this->at(0))
                           ->method('getStatusCode')
                           ->will($this->returnValue(203));
        $this->mockResponse->expects($this->once())
                           ->method('getBody')
                           ->will($this->returnValue('my page content'));
        $this->mockResponse->expects($this->at(2))
                           ->method('addHeader')
                           ->with($this->equalTo('ETag'), $this->equalTo($this->ETag));
        $this->mockResponse->expects($this->at(3))
                           ->method('addHeader')
                           ->with($this->equalTo('Cache-Control'), $this->equalTo('private'));
        $this->mockResponse->expects($this->at(4))
                           ->method('addHeader')
                           ->with($this->equalTo('Pragma'), $this->equalTo(''));
        $this->mockResponse->expects($this->at(5))
                           ->method('setStatusCode')
                           ->with($this->equalTo('304'))
                           ->will($this->returnValue($this->mockResponse));
        $this->mockResponse->expects($this->at(6))
                           ->method('clearBody');

        $this->eTagPostInterceptor->postProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
    }
}
?>