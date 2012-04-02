<?php
/**
 * Tests for net::stubbles::websites::processors::stubAbstractProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @version     $Id: stubAbstractProcessorResolverTestCase.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubAbstractProcessorResolver');
/**
 * Helper class for the test
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 */
abstract class TeststubAbstractProcessorResolver extends stubAbstractProcessorResolver
{
    /**
     * constructor
     *
     * @param stubInjector $injector
     */
    public function __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }
}
/**
 * Tests for net::stubbles::websites::processors::stubAbstractProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @deprecated
 * @group       websites
 * @group       websites_processors
 */
class stubAbstractProcessorResolverTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  TeststubAbstractProcessorResolver
     */
    protected $abstractProcessorResolver;
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
     * set up test environment
     */
    public function setUp()
    {
        $binder             = new stubBinder();
        $this->mockRequest  = $this->getMock('stubRequest');
        $this->mockSession  = $this->getMock('stubSession');
        $this->mockResponse = $this->getMock('stubResponse');
        $binder->bind('stubRequest')->toInstance($this->mockRequest);
        $binder->bind('stubSession')->toInstance($this->mockSession);
        $binder->bind('stubResponse')->toInstance($this->mockResponse);
        $this->abstractProcessorResolver = $this->getMock('TeststubAbstractProcessorResolver',
                                                          array('doResolve',
                                                                'getInterceptorDescriptor'
                                                          ),
                                                          array($binder->getInjector())
                                           );
    }

    /**
     * test that a missing return value of doResolve() triggers an exception
     *
     * @test
     * @expectedException  stubConfigurationException
     */
    public function noProcessorThrowsException()
    {
        $this->abstractProcessorResolver->expects($this->once())
                                        ->method('doResolve')
                                        ->with($this->equalTo($this->mockRequest), $this->equalTo($this->mockSession), $this->equalTo($this->mockResponse))
                                        ->will($this->returnValue(null));
        $this->abstractProcessorResolver->resolve($this->mockRequest, $this->mockSession, $this->mockResponse);
    }

    /**
     * assure that the default processor is returned and it has all required classes
     *
     * @test
     */
    public function correctProcessor()
    {
        $this->abstractProcessorResolver->expects($this->once())
                                        ->method('doResolve')
                                        ->with($this->equalTo($this->mockRequest), $this->equalTo($this->mockSession), $this->equalTo($this->mockResponse))
                                        ->will($this->returnValue('org::stubbles::test::FooProcessor'));
        $processor = $this->abstractProcessorResolver->resolve($this->mockRequest, $this->mockSession, $this->mockResponse);
        $this->assertInstanceOf('FooProcessor', $processor);
    }
}
?>