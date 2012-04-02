<?php
/**
 * Test for net::stubbles::service::jsonrpc::stubJsonRpcProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @version     $Id: stubJsonRpcProcessorTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::stubJsonRpcProcessor',
                      'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcSubProcessor'
);
@include_once 'vfsStream/vfsStream.php';
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 */
class TeststubJsonRpcSubProcessor extends stubBaseObject implements stubJsonRpcSubProcessor
{
    /**
     * does the processing of the subtask
     *
     * @param  stubRequest     $request   current request
     * @param  stubSession     $session   current session
     * @param  stubResponse    $response  current response
     * @param  stubInjector    $injector  injector instance
     * @param  stubProperties  $config    json-rpc config
     */
    public function process(stubRequest $request, stubSession $session, stubResponse $response, stubInjector $injector, stubProperties $config)
    {
        // intentionally empty
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 */
class TeststubJsonRpcProcessor extends stubJsonRpcProcessor
{
    /**
     * access to protected subprocessor dispatcher
     *
     * @return  string
     */
    public function callGetSubProcessorClassName()
    {
        return $this->getSubProcessorClassName();
    }
}
/**
 * Tests for net::stubbles::service::jsonrpc::stubJsonRpcProcessor.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @group       service_jsonrpc
 */
class stubJsonRpcProcessorTestCase extends PHPUnit_Framework_TestCase
{
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
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Test for feed loading required vfsStream, see http://vfs.bovigo.org/.');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('config'));
        vfsStream::newFile('json-rpc-service.ini')
                 ->at(vfsStreamWrapper::getRoot())
                 ->withContent("[config]\nnamespace=foo\n\n[classmap]\n");
        $this->mockRequest  = $this->getMock('stubRequest');
        $this->mockSession  = $this->getMock('stubSession');
        $this->mockResponse = $this->getMock('stubResponse');
        $this->injector     = new stubInjector();
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $constructor = new stubReflectionMethod('stubJsonRpcProcessor', '__construct');
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        
        $params = $constructor->getParameters();
        $this->assertTrue($params[4]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.config.path', $params[4]->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function routeNameIsAlwaysNull()
    {
        $jsonRpcProcessor = $this->getMock('stubJsonRpcProcessor',
                                           array('getSubProcessorClassName'),
                                           array($this->mockRequest,
                                                 $this->mockSession,
                                                 $this->mockResponse,
                                                 $this->injector,
                                                 vfsStream::url('config')
                                           )
                                );
        $this->assertNull($jsonRpcProcessor->getRouteName());
    }

    /**
     * @test
     */
    public function processAddsDefaultContentTypeToResponseIfNoContentTypeConfigured()
    {
        $jsonRpcProcessor = $this->getMock('stubJsonRpcProcessor',
                                           array('getSubProcessorClassName'),
                                           array($this->mockRequest,
                                                 $this->mockSession,
                                                 $this->mockResponse,
                                                 $this->injector,
                                                 vfsStream::url('config')
                                           )
                                );
        $jsonRpcProcessor->expects($this->once())
                         ->method('getSubProcessorClassName')
                         ->will($this->returnValue('TeststubJsonRpcSubProcessor'));
        $this->mockResponse->expects($this->once())
                           ->method('addHeader')
                           ->with($this->equalTo('Content-Type'), $this->equalTo('application/json'));
        $this->assertSame($jsonRpcProcessor, $jsonRpcProcessor->process());
    }

    /**
     * @test
     */
    public function processAddsContentTypeFromConfigToResponse()
    {
        vfsStreamWrapper::getRoot()
                        ->getChild('json-rpc-service.ini')
                        ->setContent("[config]\nnamespace=foo\ncontent-type=\"text/json\"\n\n[classmap]\n");
        $jsonRpcProcessor = $this->getMock('stubJsonRpcProcessor',
                                           array('getSubProcessorClassName'),
                                           array($this->mockRequest,
                                                 $this->mockSession,
                                                 $this->mockResponse,
                                                 $this->injector,
                                                 vfsStream::url('config')
                                           )
                                );
        $jsonRpcProcessor->expects($this->once())
                         ->method('getSubProcessorClassName')
                         ->will($this->returnValue('TeststubJsonRpcSubProcessor'));
        $this->mockResponse->expects($this->once())
                           ->method('addHeader')
                           ->with($this->equalTo('Content-Type'), $this->equalTo('text/json'));
        $this->assertSame($jsonRpcProcessor, $jsonRpcProcessor->process());
    }

    /**
     * @test
     */
    public function postRequestDelegatedToPostSubProcessor()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getMethod')
                          ->will($this->returnValue('post'));
        $jsonRpcProcessor = new TeststubJsonRpcProcessor($this->mockRequest,
                                                         $this->mockSession,
                                                         $this->mockResponse,
                                                         $this->injector,
                                                         vfsStream::url('config')
                                );
        $this->assertEquals('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcPostSubProcessor',
                            $jsonRpcProcessor->callGetSubProcessorClassName()
        );
    }

    /**
     * test processing generateProxy request
     *
     * @test
     */
    public function generateProxyRequest()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getMethod')
                          ->will($this->returnValue('get'));
        $this->mockRequest->expects($this->once())
                          ->method('hasParam')
                          ->will($this->returnValue(true));
        $jsonRpcProcessor = new TeststubJsonRpcProcessor($this->mockRequest,
                                                         $this->mockSession,
                                                         $this->mockResponse,
                                                         $this->injector,
                                                         vfsStream::url('config')
                                );
        $this->assertEquals('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateProxiesSubProcessor',
                            $jsonRpcProcessor->callGetSubProcessorClassName()
        );
    }

    /**
     * @test
     */
    public function smdRequestDelegatedToSmdSubProcessor()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getMethod')
                          ->will($this->returnValue('get'));
        $this->mockRequest->expects($this->exactly(2))
                          ->method('hasParam')
                          ->will($this->onConsecutiveCalls(false, true));
        $jsonRpcProcessor = new TeststubJsonRpcProcessor($this->mockRequest,
                                                         $this->mockSession,
                                                         $this->mockResponse,
                                                         $this->injector,
                                                         vfsStream::url('config')
                                );
        $this->assertEquals('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateSmdSubProcessor',
                            $jsonRpcProcessor->callGetSubProcessorClassName()
        );
    }

    /**
     * @test
     */
    public function getRequestDelegatedToGetSubprocessor()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getMethod')
                          ->will($this->returnValue('get'));
        $this->mockRequest->expects($this->exactly(2))
                          ->method('hasParam')
                          ->will($this->returnValue(false));
        $jsonRpcProcessor = new TeststubJsonRpcProcessor($this->mockRequest,
                                                         $this->mockSession,
                                                         $this->mockResponse,
                                                         $this->injector,
                                                         vfsStream::url('config')
                                );
        $this->assertEquals('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGetSubProcessor',
                            $jsonRpcProcessor->callGetSubProcessorClassName()
        );
    }
}
?>