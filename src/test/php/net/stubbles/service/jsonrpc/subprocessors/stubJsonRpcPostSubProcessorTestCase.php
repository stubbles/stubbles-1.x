<?php
/**
 * Test for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcPostSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors_test
 * @version     $Id: stubJsonRpcPostSubProcessorTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubJsonFilter',
                      'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcPostSubProcessor'
);
require_once dirname(__FILE__) . '/TestService.php';
/**
 * Tests for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcPostSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @group       service_jsonrpc
 */
class stubJsonRpcPostSubProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubJsonRpcPostSubProcessor
     */
    protected $jsonRpcPostSubProcessor;
    /**
     * mocked request to use
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked session to use
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
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * properties
     *
     * @var  stubProperties
     */
    protected $properties;

    /**
     * Typical error message.
     *
     * @var  string
     */
    const ERROR_MSG   = "Invalid JSON-RPC request. Should have this form: {['service':...,]'method':...,'params':...,'id':...} (with double instead of single quotation marks)";
    const MODE_DECODE = stubJsonRpcPostSubProcessor::MODE_DECODE;
    const MODE_ENCODE = stubJsonRpcPostSubProcessor::MODE_ENCODE;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->jsonRpcPostSubProcessor = new stubJsonRpcPostSubProcessor();
        $this->mockRequest             = $this->getMock('stubRequest');
        $this->mockSession             = $this->getMock('stubSession');
        $this->mockResponse            = $this->getMock('stubResponse');
        $this->injector                = new stubInjector();
        $this->properties              = new stubProperties(array('classmap' => array('Test' => 'TestService',
                                                                                      'Nope' => 'DoesNotExist'
                                                                                )
                                                            )
                                         );
    }

    /**
     * helper method for some tests
     *
     * @param  string  $value
     */
    protected function setMockRequestGetBodyReturnValue($value)
    {
        $filterFactory = $this->getMock('stubFilterFactory');
        $filterFactory->expects($this->once())
                      ->method('createForType')
                      ->will($this->returnValue(new stubFilterBuilder(new stubJsonFilter(), $this->getMock('stubRequestValueErrorFactory'))));
        $this->mockRequest->expects($this->once())
                          ->method('readBody')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $filterFactory,
                                                                                  'body',
                                                                                  $value
                                                    )
                                 )
                            );

    }
    /**
     * test processing valid post request (which is just a 'notifictaion' which souldn't produce an response)
     *
     * @test
     * @link  http://json-rpc.org/wiki/specification#a1.3Notification
     */
    public function processPostRequestValidRequestNotifictaion()
    {
        $this->setMockRequestGetBodyReturnValue('{"service":"Test","method":"add","params":[1,2],"id":null}');
        $this->mockResponse->expects($this->never())
                           ->method('write')->withAnyParameters();
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing valid post request
     *
     * @test
     */
    public function processPostRequestValidRequestWithServiceProperty()
    {
        $this->setMockRequestGetBodyReturnValue('{"service":"Test","method":"add","params":[1,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":3,"error":null}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing valid post request
     *
     * @test
     */
    public function processPostRequestValidRequestWithDateString()
    {
        $this->setMockRequestGetBodyReturnValue('{"service":"Test","method":"addOneDay","params":["new Date(Date.UTC(2006,5,20,22,18,42,223))"],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":"new Date(Date.UTC(2006,5,21,22,18,42,000))","error":null}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing valid post request
     *
     * @test
     */
    public function processPostRequestValidRequestWithDateStringArray()
    {
        $this->setMockRequestGetBodyReturnValue('{"service":"Test","method":"addOneDay","params":["new Date(Date.UTC(2006,5,20,22,18,42,223))",true],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":["new Date(Date.UTC(2006,5,21,22,18,42,000))"],"error":null}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing valid post request
     *
     * @test
     */
    public function processPostRequestValidRequestWithClassAndMethodInMethodProperty()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Test.add","params":[1,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":3,"error":null}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing valid post request
     *
     * @test
     */
    public function processPostRequestWithArbitraryPropertyOrder()
    {
        $this->setMockRequestGetBodyReturnValue('{"params":[1,2],"id":1,"method":"Test.add"}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":3,"error":null}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing invalid post request
     *
     * @test
     */
    public function processPostRequestRequestWithoutId()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Dummy.add","params":[2,2]}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":null,"result":null,"error":"'. self::ERROR_MSG .'"}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing invalid post request
     *
     * @test
     */
    public function processPostRequestRequestWithoutMethod()
    {
        $this->setMockRequestGetBodyReturnValue('{"params":[2,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":null,"error":"'. self::ERROR_MSG .'"}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing invalid post request
     *
     * @test
     */
    public function processPostRequestRequestWithoutParams()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Dummy.add","id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":null,"error":"'. self::ERROR_MSG .'"}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing invalid post request
     *
     * @test
     */
    public function processPostRequestRequestWithInvalidMethod()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"invalid","params":[2,2],"id":1}');
        $errorMessage = 'Invalid request: method-Pattern has to be <className>.<methodName> or service property has to be set.';
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":null,"error":"' . $errorMessage . '"}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing invalid post request
     *
     * @test
     */
    public function processPostRequestRequestWithNonExistingClass()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"DoesNotExist.add","params":[2,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":null,"error":"Unknown class DoesNotExist."}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing invalid post request
     *
     * @test
     */
    public function processPostRequestRequestWithMissingClass()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Nope.add","params":[2,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":null,"error":"Class DoesNotExist does not exist"}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing invalid post request
     *
     * @test
     */
    public function processPostRequestRequestWithUnknownMethod()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Test.sub","params":[2,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":null,"error":"Unknown method Test.sub."}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing invalid post request
     *
     * @test
     */
    public function processPostRequestRequestWithNoWebMethod()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Test.mod","params":[2,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":null,"error":"Method Test.mod is not a WebMethod."}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * @test
     */
    public function processPostRequestRequestWithToLessParamsResultsInErrorMessage()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Test.add","params":[2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":null,"error":"Invalid amount of parameters passed."}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * @test
     */
    public function processPostRequestRequestWithTooMuchParamsIgnoresAdditionalParams()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Test.add","params":[2,2,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":4,"error":null}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing correct post request
     *
     * @test
     */
    public function processPostRequestRequest()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"Test.add","params":[2,2],"id":1}');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":4,"error":null}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * test processing correct post request with separate classname
     *
     * @test
     */
    public function processPostRequestRequestWithSeparateClassName()
    {
        $this->setMockRequestGetBodyReturnValue('{"method":"add","params":[2,2],"id":1}');
        $this->mockRequest->expects($this->once())
                          ->method('hasParam')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  '__class',
                                                                                  'Test'
                                                    )
                                 )
                            );
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('{"id":1,"result":4,"error":null}'));
        $this->jsonRpcPostSubProcessor->process($this->mockRequest,
                                                $this->mockSession,
                                                $this->mockResponse,
                                                $this->injector,
                                                $this->properties
        );
    }

    /**
     * Decode a wrapped date string. Noted in JSON = [date].
     *
     * @test
     */
    public function decodeDate1()
    {
        $arr = array('new Date(Date.UTC(2006,5,20,22,18,42,223))');
        $this->jsonRpcPostSubProcessor->walkForDateAndProcess($arr, stubJsonRpcPostSubProcessor::MODE_DECODE);
        $this->assertInstanceOf('stubDate', $arr[0]);
    }

    /**
     * Decode a wrapped date string. Noted in JSON = [{{date}}].
     *
     * @test
     */
    public function decodeDate2()
    {
        $arr = array();
        $obj2 = new stdClass();
        $obj2->b = 'foo';
        $obj2->c = 'new Date(Date.UTC(2006,5,20,22,18,42,223))';
        $obj = new stdClass();
        $obj->a = $obj2;
        $arr[] = $obj;

        $this->jsonRpcPostSubProcessor->walkForDateAndProcess($arr, self::MODE_DECODE);
        $stdObj = $arr[0];
        $this->assertInstanceOf('stubDate', $stdObj->a->c);
    }

    /**
     * Decode a wrapped date string. Noted in JSON = [[[date]]].
     *
     * @test
     */
    public function decodeDate3()
    {
        $arr[] = array('a', array('a' => 'new Date(Date.UTC(2006,5,20,22,18,42,223))', 'b' => 12));

        $this->jsonRpcPostSubProcessor->walkForDateAndProcess($arr, self::MODE_DECODE);
        $this->assertInstanceOf('stubDate', $arr[0][1]['a']);
    }

    /**
     * Decode a wrapped date string. Noted in JSON = [[{date}]].
     *
     * @test
     */
    public function decodeDate4()
    {
        $arr = array();
        $obj = new stdClass();
        $obj->a = 'foo';
        $obj->c = 'new Date(Date.UTC(2006,5,20,22,18,42,223))';
        $arr[] = array('a','b' => $obj);

        $this->jsonRpcPostSubProcessor->walkForDateAndProcess($arr, self::MODE_DECODE);
        $this->assertInstanceOf('stubDate', $arr[0]['b']->c);
    }

    /**
     * Decode a wrapped date string. Noted in JSON = [{[date]}].
     *
     * @test
     */
    public function decodeDate5()
    {
        $arr = array();
        $obj = new stdClass();
        $obj->a = array('a' => 'new Date(Date.UTC(2006,5,20,22,18,42,223))', 'b' => 12);
        $arr[] = $obj;

        $this->jsonRpcPostSubProcessor->walkForDateAndProcess($arr, self::MODE_DECODE);
        $this->assertInstanceOf('stubDate', $arr[0]->a['a']);
        $this->assertSame(12, $arr[0]->a['b']);
    }

    /**
     * Encode a wrapped stubDate. Noted in JSON = [[date]].
     *
     * @test
     */
    public function encodeDate1()
    {
        $arr = array(array(new stubDate('2006-05-01 22:1:1', null)));
        $this->jsonRpcPostSubProcessor->walkForDateAndProcess($arr, self::MODE_ENCODE);
        $this->assertEquals('new Date(Date.UTC(2006,5,1,22,1,1,000))', $arr[0][0]);
    }

    /**
     * Encode a wrapped stubDate. Noted in JSON = {date}.
     *
     * @test
     */
    public function encodeDate2()
    {
        $obj = new stdClass();
        $obj->a = new stubDate('2006-05-01 22:1:1', null);
        $this->jsonRpcPostSubProcessor->walkForDateAndProcess($obj, self::MODE_ENCODE);
        $this->assertEquals('new Date(Date.UTC(2006,5,1,22,1,1,000))', $obj->a);
    }

    /**
     * Encode a wrapped stubDate. Noted in JSON = {[date]}.
     *
     * @test
     */
    public function encodeDate3()
    {
        $obj = new stdClass();
        $obj->a = array(new stubDate('2006-05-01 22:1:1', null));
        $this->jsonRpcPostSubProcessor->walkForDateAndProcess($obj, self::MODE_ENCODE);
        $this->assertEquals('new Date(Date.UTC(2006,5,1,22,1,1,000))', $obj->a[0]);
    }
}
?>