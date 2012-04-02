<?php
/**
 * Tests for net::stubbles::websites::processors::stubSimpleProcessorResolver
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @version     $Id: stubSimpleProcessorResolverTestCase.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubSimpleProcessorResolver');
/**
 * Helper class to access the doResolve() method directly and circumvent the
 * net::stubbles::websites::processors::stubAbstractProcessorResolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 */
class TeststubSimpleProcessorResolver extends stubSimpleProcessorResolver
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
 * Tests for net::stubbles::websites::processors::stubSimpleProcessorResolver
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @deprecated
 * @group       websites
 * @group       websites_processors
 */
class stubSimpleProcessorResolverTestCase extends PHPUnit_Framework_TestCase
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
     * binder instance
     *
     * @var  stubBinder
     */
    protected $binder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest  = $this->getMock('stubRequest');
        $this->mockSession  = $this->getMock('stubSession');
        $this->mockResponse = $this->getMock('stubResponse');
        $this->binder       = new stubBinder();
        $this->binder->bind('stubRequest')
                     ->toInstance($this->mockRequest);
    }

    /**
     * assure that the default processor is returned and it has all required classes
     *
     * @test
     */
    public function withProcessor()
    {
        $simpleProcessorResolver = new TeststubSimpleProcessorResolver($this->binder->getInjector(), 'org::stubbles::test::FooProcessor');
        $processor = $simpleProcessorResolver->getDoResolveReturnValue($this->mockRequest, $this->mockSession, $this->mockResponse);
        $this->assertEquals('org::stubbles::test::FooProcessor', $processor);
    }

    /**
     * interceptor descriptor should be returned based on processor
     *
     * @test
     */
    public function interceptorDescriptorIsCorrect()
    {
        $simpleProcessorResolver = new TeststubSimpleProcessorResolver($this->binder->getInjector(), 'org::stubbles::test::FooProcessor');
        $this->assertEquals('interceptors', $simpleProcessorResolver->getInterceptorDescriptor($this->mockRequest));
        
        $simpleProcessorResolver = new TeststubSimpleProcessorResolver($this->binder->getInjector(), 'org::stubbles::test::FooProcessor', 'other');
        $this->assertEquals('other', $simpleProcessorResolver->getInterceptorDescriptor($this->mockRequest));
    }
}
?>