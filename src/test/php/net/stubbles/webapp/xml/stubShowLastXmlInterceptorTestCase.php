<?php
/**
 * Tests for net::stubbles::webapp::xml::stubShowLastXmlInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_test
 * @version     $Id: stubShowLastXmlInterceptorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::stubShowLastXmlInterceptor');
/**
 * Tests for net::stubbles::webapp::xml::stubShowLastXmlInterceptor.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_test
 * @group       webapp
 * @group       webapp_xml
 */
class stubShowLastXmlInterceptorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubShowLastXMLInterceptor
     */
    protected $showLastXMLPreInterceptor;
    /**
     * mocked response instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
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
     * set up test environment
     */
    public function setUp()
    {
        $this->showLastXMLPreInterceptor = new stubShowLastXmlInterceptor();
        $this->mockRequest               = $this->getMock('stubRequest');
        $this->mockResponse              = $this->getMock('stubResponse');
        $this->mockSession               = $this->getMock('stubSession');
    }

    /**
     * @test
     */
    public function newSessionDoesNotTriggerInterceptor()
    {
        $this->mockRequest->expects($this->once())->method('hasParam')->will($this->returnValue(true));
        $this->mockSession->expects($this->once())->method('isNew')->will($this->returnValue(true));
        $this->mockRequest->expects($this->never())->method('cancel');
        $this->mockResponse->expects($this->never())->method('write');
        $this->showLastXMLPreInterceptor->preProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
    }

    /**
     * @test
     */
    public function requestDoesNotHaveValueThatRequestsLastXMLDoesNotTriggerInterceptor()
    {
        $this->mockRequest->expects($this->once())->method('hasParam')->will($this->returnValue(false));
        $this->mockSession->expects($this->never())->method('isNew');
        $this->mockRequest->expects($this->never())->method('cancel');
        $this->mockResponse->expects($this->never())->method('write');
        $this->showLastXMLPreInterceptor->preProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
    }

    /**
     * @test
     */
    public function ifRequestHasValueAndSessionIsNotNewRequestIsCancelledAndResponseFilledWithLastResult()
    {
        $this->mockRequest->expects($this->once())->method('hasParam')->will($this->returnValue(true));
        $this->mockSession->expects($this->once())->method('isNew')->will($this->returnValue(false));
        $this->mockSession->expects($this->once())->method('getValue')->will($this->returnValue('foo'));
        $this->mockRequest->expects($this->once())->method('cancel');
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('foo'));
        $this->showLastXMLPreInterceptor->preProcess($this->mockRequest, $this->mockSession, $this->mockResponse);
    }
}
?>