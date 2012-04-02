<?php
/**
 * Tests for net::stubbles::webapp::processor::stubAbstractProcessor.
 *
 * @package     stubbles
 * @subpackage  webapp_processor_test
 * @version     $Id: stubAbstractProcessorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubModifiableWebRequest',
                      'net::stubbles::webapp::processor::stubAbstractProcessor'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_processor_test
 */
class TeststubAbstractProcessor extends stubAbstractProcessor
{
    /**
     * returns the name of the current route
     *
     * @return  string
     */
    public function getRouteName() { }

    /**
     * processes the request
     * 
     * @return  stubProcessor
     */
    public function process() { }
}
/**
 * Tests for net::stubbles::webapp::processor::stubAbstractProcessor.
 *
 * @package     stubbles
 * @subpackage  webapp_processor_test
 * @group       webapp
 * @group       webapp_processor
 */
class stubAbstractProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  TeststubAbstractProcessor
     */
    protected $abstractProcessor;
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
        $this->mockRequest       = $this->getMock('stubRequest');
        $this->mockSession       = $this->getMock('stubSession');
        $this->mockResponse      = $this->getMock('stubResponse');
        $this->abstractProcessor = new TeststubAbstractProcessor($this->mockRequest,
                                                                 $this->mockSession,
                                                                 $this->mockResponse
                                   );
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationPresent()
    {
        $this->assertTrue($this->abstractProcessor->getClass()->getConstructor()->hasAnnotation('Inject'));
    }

    /**
     * @test
     */
    public function startupsDoesNothingButReturningItself()
    {
        $this->assertSame($this->abstractProcessor,
                          $this->abstractProcessor->startup(new stubUriRequest('/'))
        );
    }

    /**
     * returns always <null> for required role
     *
     * @test
     */
    public function returnsAlwaysDefaultRole()
    {
        $this->assertNull($this->abstractProcessor->getRequiredRole(null));
        $this->assertEquals('default', $this->abstractProcessor->getRequiredRole('default'));
    }

    /**
     * is never cachable by default
     *
     * @test
     */
    public function isNeverCachableByDefault()
    {
        $this->assertFalse($this->abstractProcessor->isCachable());
    }

    /**
     * no cache vars delivered by default
     *
     * @test
     */
    public function getCacheVarsReturnsEmptyArrayByDefault()
    {
        $this->assertEquals(array(), $this->abstractProcessor->getCacheVars());
    }

    /**
     * a processor never forces ssl by default
     *
     * @test
     */
    public function neverForcesSslByDefault()
    {
        $this->assertFalse($this->abstractProcessor->forceSsl());
    }

    /**
     * ssl evaluates to true if validation returns true, should only be evaluated once
     *
     * @test
     */
    public function isSslShouldBeTrueAndOnlyEvaluatedOnce()
    {
        $requestValue = new stubValidatingRequestValue('SERVER_PORT', '443');
        $this->mockRequest->expects($this->once())
                          ->method('validateHeader')
                          ->will($this->returnValue($requestValue));
        $this->assertTrue($this->abstractProcessor->isSsl());
        $this->assertTrue($this->abstractProcessor->isSsl());
    }

    /**
     * ssl evaluates to false if validation returns true, should only be evaluated once
     *
     * @test
     */
    public function isSSLShouldBeFalseAndOnlyEvaluatedOnce()
    {
        $requestValue = new stubValidatingRequestValue('SERVER_PORT', '80');
        $this->mockRequest->expects($this->once())
                          ->method('validateHeader')
                          ->will($this->returnValue($requestValue));
        $this->assertFalse($this->abstractProcessor->isSsl());
        $this->assertFalse($this->abstractProcessor->isSsl());
    }

    /**
     * @test
     */
    public function cleanupDoesNothingButReturningItself()
    {
        $this->assertSame($this->abstractProcessor,
                          $this->abstractProcessor->cleanup()
        );
    }

    /**
     * @test
     */
    public function integerPortForSslCheckRecognized()
    {
        $request = new stubModifiableWebRequest($this->getMock('stubFilterFactory'));
        $request->setHeader('SERVER_PORT', 443);
        $this->abstractProcessor = new TeststubAbstractProcessor($request,
                                                                 $this->mockSession,
                                                                 $this->mockResponse
                                   );
        $this->assertTrue($this->abstractProcessor->isSsl());
    }

    /**
     * @test
     */
    public function stringPortForSslCheckRecognized()
    {
        $request = new stubModifiableWebRequest($this->getMock('stubFilterFactory'));
        $request->setHeader('SERVER_PORT', '443');
        $this->abstractProcessor = new TeststubAbstractProcessor($request,
                                                                 $this->mockSession,
                                                                 $this->mockResponse
                                   );
        $this->assertTrue($this->abstractProcessor->isSsl());
    }
}
?>