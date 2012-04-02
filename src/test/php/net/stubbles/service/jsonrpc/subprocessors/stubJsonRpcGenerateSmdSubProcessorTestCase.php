<?php
/**
 * Test for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateSmdSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors_test
 * @version     $Id: stubJsonRpcGenerateSmdSubProcessorTestCase.php 2683 2010-08-24 19:33:16Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateSmdSubProcessor');
/**
 * Tests for net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateSmdSubProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @group       service_jsonrpc
 */
class stubJsonRpcGenerateSmdSubProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubJsonRpcGenerateSmdSubProcessor
     */
    protected $jsonRpcGenerateSmdSubProcessor;
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
        $this->jsonRpcGenerateSmdSubProcessor = $this->getMock('stubJsonRpcGenerateSmdSubProcessor',
                                                               array('getServiceUrl',
                                                                     'handleException',
                                                                     'getSmdGenerator'
                                                               )
                                                );
        $this->mockRequest                    = $this->getMock('stubRequest');
        $this->mockSession                    = $this->getMock('stubSession');
        $this->mockResponse                   = $this->getMock('stubResponse');
        $this->injector                       = new stubInjector();
        $this->properties                     = new stubProperties(array('classmap' => array('Test' => 'TestService',
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
                                                                                  '__smd',
                                                                                  'stubbles.json.proxy.Test'
                                                    )
                                 )
                            );
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->once())
                                             ->method('getServiceUrl')
                                             ->with($this->equalTo($this->mockRequest))
                                             ->will($this->returnValue('serviceUrl'));
        $this->mockSmdGenerator = $this->getMock('stubSmdGenerator', array(), array('serviceUrl'));
        $this->mockSmdGenerator->expects($this->once())
                               ->method('generateSmd')
                               ->with($this->equalTo('TestService'), $this->equalTo('Test'))
                               ->will($this->returnValue('smdDescription'));
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->once())
                                             ->method('getSmdGenerator')
                                             ->with($this->equalTo('serviceUrl&__class=stubbles.json.proxy.Test'))
                                             ->will($this->returnValue($this->mockSmdGenerator));
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('smdDescription'));
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->never())
                                             ->method('handleException');
        $this->jsonRpcGenerateSmdSubProcessor->process($this->mockRequest, $this->mockSession, $this->mockResponse, $this->injector, $this->properties);
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
                                                                                  '__smd',
                                                                                  'foo.bar.Test'
                                                    )
                                 )
                            );
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->once())
                                             ->method('getServiceUrl')
                                             ->with($this->equalTo($this->mockRequest))
                                             ->will($this->returnValue('serviceUrl'));
        $this->mockSmdGenerator = $this->getMock('stubSmdGenerator', array(), array('serviceUrl'));
        $this->mockSmdGenerator->expects($this->once())
                               ->method('generateSmd')
                               ->with($this->equalTo('TestService'), $this->equalTo('Test'))
                               ->will($this->returnValue('smdDescription'));
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->once())
                                             ->method('getSmdGenerator')
                                             ->with($this->equalTo('serviceUrl&__class=foo.bar.Test'))
                                             ->will($this->returnValue($this->mockSmdGenerator));
        $this->mockResponse->expects($this->once())->method('write')->with($this->equalTo('smdDescription'));
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->never())
                                             ->method('handleException');
        $this->properties = new stubProperties(array('classmap' => array('Test' => 'TestService',
                                                                         'Nope' => 'DoesNotExist'
                                                                   ),
                                                     'config'   => array('namespace' => 'foo.bar')
                                               )
                            );
        $this->jsonRpcGenerateSmdSubProcessor->process($this->mockRequest, $this->mockSession, $this->mockResponse, $this->injector, $this->properties);
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
                                                                                  '__smd',
                                                                                  'stubbles.json.proxy.Test'
                                                    )
                                 )
                            );
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->once())
                                             ->method('getServiceUrl')
                                             ->with($this->equalTo($this->mockRequest))
                                             ->will($this->returnValue('serviceUrl'));
        $this->mockSmdGenerator = $this->getMock('stubSmdGenerator', array(), array('serviceUrl'));
        $exception = new Exception('exceptionMessage');
        $this->mockSmdGenerator->expects($this->once())
                               ->method('generateSmd')
                               ->with($this->equalTo('TestService'), $this->equalTo('Test'))
                               ->will($this->throwException($exception));
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->once())
                                             ->method('getSmdGenerator')
                                             ->with($this->equalTo('serviceUrl&__class=stubbles.json.proxy.Test'))
                                             ->will($this->returnValue($this->mockSmdGenerator));
        $this->mockResponse->expects($this->never())->method('write');
        $this->jsonRpcGenerateSmdSubProcessor->expects($this->once())
                                             ->method('handleException');
        // checking the params fails with PHPUnit >= 3.4.0 and causes infinite
        // loop and/or memory corruption
//                                             ->with($this->anything(),
//                                                    $this->equalTo($this->mockResponse),
//                                                    $this->equalTo('Generation of SMD for TestService failed.')
//                                               );
        $this->jsonRpcGenerateSmdSubProcessor->process($this->mockRequest, $this->mockSession, $this->mockResponse, $this->injector, $this->properties);
    }
}
?>