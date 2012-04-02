<?php
/**
 * Test for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGetSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors_test
 * @version     $Id: stubJsonRpcGetSubProcessorTestCase.php 2683 2010-08-24 19:33:16Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGetSubProcessor');
require_once dirname(__FILE__) . '/TestService.php';
/**
 * Tests for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGetSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @group       service_jsonrpc
 */
class stubJsonRpcGetSubProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubJsonRpcGetSubProcessor
     */
    protected $jsonRpcGetSubProcessor;
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
     * set up test environment
     */
    public function setUp()
    {
        $this->jsonRpcGetSubProcessor = new stubJsonRpcGetSubProcessor();
        $this->mockRequest            = $this->getMock('stubRequest');
        $this->mockSession            = $this->getMock('stubSession');
        $this->mockResponse           = $this->getMock('stubResponse');
        $this->injector               = new stubInjector();
        $this->properties             = new stubProperties(array('classmap' => array('Test' => 'TestService',
                                                                                     'Nope' => 'DoesNotExist'
                                                                               )
                                                           )
                                        );
    }

    /**
     * @test
     */
    public function processGetRequestWithoutIdReturnsError()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'id',
                                                                                  null
                                                    )
                                 )
                            );
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":null,"result":null,"error":"Invalid request: No id given."}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }

    /**
     * @test
     */
    public function processGetRequestWithoutMethodReturnsError()
    {
        $this->mockRequest->expects($this->exactly(2))
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'id',
                                                                                         '123456'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'method',
                                                                                         null
                                                           )
                                 )
                            );
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":"123456","result":null,"error":"Invalid request: No method given."}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }

    /**
     * @test
     */
    public function processGetRequestWithInvalidMethodReturnsError()
    {
        $this->mockRequest->expects($this->exactly(2))
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'id',
                                                                                         '123456'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'method',
                                                                                         'invalid'
                                                           )
                                 )
                            );

        $errorMessage = 'Invalid request: method-Pattern has to be <className>.<methodName> or service property has to be set.';
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":"123456","result":null,"error":"'.$errorMessage.'"}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }

    /**
     * @test
     */
    public function processGetRequestWithNonExistingClassReturnsError()
    {
        $this->mockRequest->expects($this->exactly(2))
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'id',
                                                                                         '123456'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'method',
                                                                                         'DoesNotExist.add'
                                                           )
                                 )
                            );
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":"123456","result":null,"error":"Unknown class DoesNotExist."}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }

    /**
     * @test
     */
    public function processGetRequestWithMissingClassReturnsError()
    {
        $this->mockRequest->expects($this->exactly(2))
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'id',
                                                                                         '123456'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'method',
                                                                                         'Nope.add'
                                                           )
                                 )
                            );
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":"123456","result":null,"error":"Class DoesNotExist does not exist"}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }

    /**
     * @test
     */
    public function processGetRequestWithUnknownMethodReturnsError()
    {
        $this->mockRequest->expects($this->exactly(2))
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'id',
                                                                                         '123456'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'method',
                                                                                         'Test.sub'
                                                           )
                                 )
                            );
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":"123456","result":null,"error":"Unknown method Test.sub."}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }

    /**
     * @test
     */
    public function processGetRequestWithNoWebMethodReturnsError()
    {
        $this->mockRequest->expects($this->exactly(2))
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'id',
                                                                                         '123456'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'method',
                                                                                         'Test.mod'
                                                           )
                                 )
                            );
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":"123456","result":null,"error":"Method Test.mod is not a WebMethod."}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }

    /**
     * @test
     */
    public function processGetRequestWithTooLessParamsReturnsError()
    {
        $this->mockRequest->expects($this->exactly(4))
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'id',
                                                                                         '123456'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'method',
                                                                                         'Test.add'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'a',
                                                                                         2
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'b',
                                                                                         null
                                                           )
                                 )
                            );
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":"123456","result":null,"error":"Param b is missing."}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }

    /**
     * @test
     */
    public function processGetRequestWithValidParametersReturnsResult()
    {
        $this->mockRequest->expects($this->exactly(4))
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'id',
                                                                                         '123456'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'method',
                                                                                         'Test.add'
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'a',
                                                                                         2
                                                           ),
                                                           new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                         $this->getMock('stubFilterFactory'),
                                                                                         'b',
                                                                                         2
                                                           )
                                 )
                            );
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('{"id":"123456","result":4,"error":null}'));
        $this->jsonRpcGetSubProcessor->process($this->mockRequest,
                                               $this->mockSession,
                                               $this->mockResponse,
                                               $this->injector,
                                               $this->properties
        );
    }
}
?>