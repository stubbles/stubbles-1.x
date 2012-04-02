<?php
/**
 * Test for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateProxiesSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors_test
 * @version     $Id: stubJsonRpcGenerateProxiesSubProcessorTestCase.php 2683 2010-08-24 19:33:16Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateProxiesSubProcessor');
/**
 * Tests for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateProxiesSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @group       service_jsonrpc
 */
class stubJsonRpcGenerateProxiesSubProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubJsonRpcGenerateProxiesSubProcessor
     */
    protected $jsonRpcGenerateProxiesSubProcessor;
    /**
     * mocked request to use
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked response instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * mocked session to use
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
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
        $this->jsonRpcGenerateProxiesSubProcessor = $this->getMock('stubJsonRpcGenerateProxiesSubProcessor',
                                                                   array('getServiceUrl',
                                                                         'handleException',
                                                                         'getProxyGenerator'
                                                                   )
                                                    );
        $this->mockRequest                        = $this->getMock('stubRequest');
        $this->mockSession                        = $this->getMock('stubSession');
        $this->mockResponse                       = $this->getMock('stubResponse');
        $this->injector                           = new stubInjector();
        $this->properties                         = new stubProperties(array('classmap' => array('Test' => 'TestService',
                                                                                                 'Nope' => 'DoesNotExist'
                                                                                           )
                                                                       )
                                                    );
    }

    /**
     * retrieve service url
     *
     * @test
     */
    public function successful()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  '__generateProxy',
                                                                                  '__all'
                                                    )
                                 )
                            );
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->never())->method('getServiceUrl');
        $this->mockProxyGenerator = $this->getMock('stubJsonRpcProxyGenerator');
        $this->mockProxyGenerator->expects($this->at(0))
                                 ->method('generateJavascriptProxy')
                                 ->with($this->equalTo('TestService'), $this->equalTo('Test'))
                                 ->will($this->returnValue('javascript proxy1'));
        $this->mockProxyGenerator->expects($this->at(1))
                                 ->method('generateJavascriptProxy')
                                 ->with($this->equalTo('DoesNotExist'), $this->equalTo('Nope'))
                                 ->will($this->returnValue('javascript proxy2'));
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->once())
                                                 ->method('getProxyGenerator')
                                                 ->will($this->returnValue($this->mockProxyGenerator));
        $this->mockResponse->expects($this->at(0))->method('write')->with($this->equalTo("stubbles.json.proxy = {};\n\n"));
        $this->mockResponse->expects($this->at(1))->method('write')->with($this->equalTo('javascript proxy1'));
        $this->mockResponse->expects($this->at(2))->method('write')->with($this->equalTo('javascript proxy2'));
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->never())
                                                 ->method('handleException');
        $this->jsonRpcGenerateProxiesSubProcessor->process($this->mockRequest, $this->mockSession, $this->mockResponse, $this->injector, $this->properties);
    }

    /**
     * retrieve service url
     *
     * @test
     */
    public function successfulWithConfiguredNamespace()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  '__generateProxy',
                                                                                  '__all'
                                                    )
                                 )
                            );
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->never())->method('getServiceUrl');
        $this->mockProxyGenerator = $this->getMock('stubJsonRpcProxyGenerator');
        $this->mockProxyGenerator->expects($this->at(0))
                                 ->method('generateJavascriptProxy')
                                 ->with($this->equalTo('TestService'), $this->equalTo('Test'))
                                 ->will($this->returnValue('javascript proxy1'));
        $this->mockProxyGenerator->expects($this->at(1))
                                 ->method('generateJavascriptProxy')
                                 ->with($this->equalTo('DoesNotExist'), $this->equalTo('Nope'))
                                 ->will($this->returnValue('javascript proxy2'));
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->once())
                                                 ->method('getProxyGenerator')
                                                 ->will($this->returnValue($this->mockProxyGenerator));
        $this->mockResponse->expects($this->at(0))->method('write')->with($this->equalTo("foo.bar = {};\n\n"));
        $this->mockResponse->expects($this->at(1))->method('write')->with($this->equalTo('javascript proxy1'));
        $this->mockResponse->expects($this->at(2))->method('write')->with($this->equalTo('javascript proxy2'));
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->never())
                                                 ->method('handleException');
        $this->properties = new stubProperties(array('classmap' => array('Test' => 'TestService',
                                                                         'Nope' => 'DoesNotExist'
                                                                   ),
                                                     'config'   => array('namespace' => 'foo.bar')
                                               )
                            );
        $this->jsonRpcGenerateProxiesSubProcessor->process($this->mockRequest, $this->mockSession, $this->mockResponse, $this->injector, $this->properties);
    }

    /**
     * retrieve service url
     *
     * @test
     */
    public function notSuccesful()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  '__generateProxy',
                                                                                  'Nope'
                                                    )
                                 )
                            );
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->never())->method('getServiceUrl');
        $this->mockProxyGenerator = $this->getMock('stubJsonRpcProxyGenerator');
        $exception = new Exception('exceptionMessage');
        $this->mockProxyGenerator->expects($this->once())
                                 ->method('generateJavascriptProxy')
                                 ->will($this->throwException($exception));
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->once())
                                                 ->method('getProxyGenerator')
                                                 ->will($this->returnValue($this->mockProxyGenerator));
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo("stubbles.json.proxy = {};\n\n"));
        $this->jsonRpcGenerateProxiesSubProcessor->expects($this->once())
                                                 ->method('handleException')
                                                 ->with($this->equalTo($this->injector),
                                                        $this->equalTo($exception),
                                                        $this->equalTo($this->mockResponse),
                                                        $this->equalTo('Generation of proxy for DoesNotExist failed.')
                                                   );
        $this->jsonRpcGenerateProxiesSubProcessor->process($this->mockRequest, $this->mockSession, $this->mockResponse, $this->injector, $this->properties);
    }
}
?>