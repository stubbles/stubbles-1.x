<?php
/**
 * Test for net::stubbles::service::rest::index::stubIndexRestHandler.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::service::rest::index::stubIndexRestHandler');
/**
 * Test for net::stubbles::service::rest::index::stubIndexRestHandler.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.8.0
 * @group       service
 * @group       service_rest
 * @group       service_rest_index
 */
class stubIndexRestHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubIndexRestHandler
     */
    private $indexRestHandler;
    /**
     * mocked request instance
     *
     * @var  stubRestServices
     */
    private $mockRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest      = $this->getMock('stubRequest');
        $this->indexRestHandler = new stubIndexRestHandler($this->mockRequest,
                                                           array('foo' => 'org::stubbles::test::rest::FooRestService')
                                  );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->indexRestHandler->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.service.rest.handler',
                            $parameters[1]->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetModeMethod()
    {
        $this->assertTrue($this->indexRestHandler->getClass()
                                                 ->getMethod('setMode')
                                                 ->hasAnnotation('Inject')
        );
        $this->assertTrue($this->indexRestHandler->getClass()
                                                 ->getMethod('setMode')
                                                 ->getAnnotation('Inject')
                                                 ->isOptional()
        );
    }

    /**
     * @test
     */
    public function listsServicesWithoutMode()
    {
        $this->mockRequest->expects($this->once())
                          ->method('validateHeader')
                          ->with($this->equalTo('SERVER_PORT'))
                          ->will($this->returnValue(new stubValidatingRequestValue('SERVER_PORT', '80')));
        $this->mockRequest->expects($this->once())
                          ->method('getURI')
                          ->will($this->returnValue('example.net/'));
        $restServices = new stubRestServices();
        $restServices->addService(new stubRestService(new stubRestLink('self', 'http://example.net/foo'),
                                                      'foo service',
                                                      'Foo service description'
                                  )
        );
        $this->assertEquals($restServices,
                            $this->indexRestHandler->listServices()
        );
    }

    /**
     * @test
     */
    public function listsServicesWithMode()
    {
        $this->mockRequest->expects($this->once())
                          ->method('validateHeader')
                          ->with($this->equalTo('SERVER_PORT'))
                          ->will($this->returnValue(new stubValidatingRequestValue('SERVER_PORT', '443')));
        $this->mockRequest->expects($this->once())
                          ->method('getURI')
                          ->will($this->returnValue('example.net/'));
        $mockMode = $this->getMock('stubMode');
        $mockMode->expects($this->once())
                 ->method('name')
                 ->will($this->returnValue('TEST'));
        $restServices = new stubRestServices();
        $restServices->setEnvironmentName('TEST')
                     ->addService(new stubRestService(new stubRestLink('self', 'https://example.net/foo'),
                                                      'foo service',
                                                      'Foo service description'
                                  )
        );
        $this->assertEquals($restServices,
                            $this->indexRestHandler->setMode($mockMode)
                                                   ->listServices()
        );
    }
}
?>