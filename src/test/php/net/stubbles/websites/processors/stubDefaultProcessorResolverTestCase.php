<?php
/**
 * Tests for net::stubbles::websites::processors::stubDefaultProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @version     $Id: stubDefaultProcessorResolverTestCase.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubDefaultProcessorResolver');
/**
 * Helper class to access the doResolve() method directly and circumvent the
 * net::stubbles::websites::processors::stubAbstractProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 */
class TeststubDefaultProcessorResolver extends stubDefaultProcessorResolver
{
    /**
     * direct access to doResolve()
     *
     * @param   stubRequest   $request
     * @param   stubSession   $session
     * @param   stubResponse  $response
     * @return  string
     */
    public function getDoResolveReturnValue(stubRequest $request, stubSession $session, stubResponse $response)
    {
        return $this->doResolve($request, $session, $response);
    }
}
/**
 * Tests for net::stubbles::websites::processors::stubDefaultProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @deprecated
 * @group       websites
 * @group       websites_processors
 */
class stubDefaultProcessorResolverTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  TeststubDefaultProcessorResolver
     */
    protected $defaultProcessorResolver;
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
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultProcessorResolver = new TeststubDefaultProcessorResolver(new stubInjector(), 'foo', 'org::stubbles::test::FooProcessor', 'interceptors-foo');
        $this->mockRequest              = $this->getMock('stubRequest');
        $this->mockSession              = $this->getMock('stubSession');
        $this->mockResponse             = $this->getMock('stubResponse');
    }

    /**
     * helper method to add the processors to the resolver
     */
    protected function addProcessors()
    {
        $this->defaultProcessorResolver->addProcessor('bar', 'org::stubbles::test::BarProcessor', null);
        $this->defaultProcessorResolver->addProcessor('baz', 'org::stubbles::test::BazProcessor');
    }

    /**
     * helper method to create request values
     *
     * @param   string                     $value
     * @return  stubFilteringRequestValue
     */
    protected function createFilteringRequestValue($value)
    {
        return new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                             $this->getMock('stubFilterFactory'),
                                             'dummy',
                                             $value
               );
    }

    /**
     * assure that the selected processor is returned and it has all required classes
     *
     * @test
     */
    public function selectedProcessor()
    {
        $this->addProcessors();
        $this->mockRequest->expects($this->any())->method('readParam')->will($this->returnValue($this->createFilteringRequestValue('bar')));
        $this->mockSession->expects($this->once())
                          ->method('putValue')
                          ->with($this->equalTo('net.stubbles.websites.lastProcessor'), $this->equalTo('bar'));
        $this->assertEquals('interceptors', $this->defaultProcessorResolver->getInterceptorDescriptor($this->mockRequest));
        $processor = $this->defaultProcessorResolver->getDoResolveReturnValue($this->mockRequest, $this->mockSession, $this->mockResponse);
        $this->assertEquals('org::stubbles::test::BarProcessor', $processor);
    }

    /**
     * assure that the default processor is returned if selected does not exist
     *
     * @test
     */
    public function defaultFallbackProcessor()
    {
        $this->addProcessors();
        $this->mockRequest->expects($this->any())->method('readParam')->will($this->returnValue($this->createFilteringRequestValue(null)));
        $this->mockSession->expects($this->once())
                          ->method('putValue')
                          ->with($this->equalTo('net.stubbles.websites.lastProcessor'), $this->equalTo('foo'));
        $this->assertEquals('interceptors-foo', $this->defaultProcessorResolver->getInterceptorDescriptor($this->mockRequest));
        $processor = $this->defaultProcessorResolver->getDoResolveReturnValue($this->mockRequest, $this->mockSession, $this->mockResponse);
        $this->assertEquals($processor, 'org::stubbles::test::FooProcessor');
    }
}
?>