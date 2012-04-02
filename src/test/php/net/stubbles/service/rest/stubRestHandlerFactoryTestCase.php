<?php
/**
 * Test for net::stubbles::service::rest::stubRestHandlerFactory.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id: stubRestHandlerFactoryTestCase.php 3204 2011-11-02 16:12:02Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::stubRestHandlerFactory');
/**
 * Test for net::stubbles::service::rest::stubRestHandlerFactory.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.7.0
 * @group       service
 * @group       service_rest
 * @group       service_rest_webapp
 */
class stubRestHandlerFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRestHandlerFactory
     */
    protected $restHandlerFactory;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInjector       = $this->getMock('stubInjector');
        $this->restHandlerFactory = new stubRestHandlerFactory($this->mockInjector, array('foo/' => 'foo::MyRestHandler'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->restHandlerFactory->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.service.rest.handler',
                            $parameters[1]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function createHandlerReturnsNullIfUriDoesNotMatchAnyCondition()
    {
        $this->assertNull($this->restHandlerFactory->createHandler('bar/'));
    }

    /**
     * @test
     */
    public function createHandlerReturnsHandlerForMatchingUri()
    {
        $restHandler = new stdClass();
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('foo::MyRestHandler'))
                           ->will($this->returnValue($restHandler));
        $this->assertSame($restHandler, $this->restHandlerFactory->createHandler('foo/'));
    }

    /**
     * @test
     */
    public function getDispatchUriReturnsRemainingUriAfterHandlerUri()
    {
        $restHandler = new stdClass();
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('foo::MyRestHandler'))
                           ->will($this->returnValue($restHandler));
        $this->assertSame($restHandler, $this->restHandlerFactory->createHandler('foo/some/more'));
        $this->assertEquals('some/more', $this->restHandlerFactory->getDispatchUri('foo/some/more'));
    }

    /**
     * @test
     */
    public function getDispatchUriReturnsNullIfUriDoesNotMatchPreviousHandlerUriCondition()
    {
        $restHandler = new stdClass();
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('foo::MyRestHandler'))
                           ->will($this->returnValue($restHandler));
        $this->assertSame($restHandler, $this->restHandlerFactory->createHandler('foo/some/more'));
        $this->assertNull($this->restHandlerFactory->getDispatchUri('bar/'));
    }

    /**
     * @since  1.8.0
     * @test
     * @group  service_rest_index
     */
    public function createHandlerReturnsIndexHandlerForEmptyUri()
    {
        $restHandler = new stdClass();
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('net::stubbles::service::rest::index::stubIndexRestHandler'))
                           ->will($this->returnValue($restHandler));
        $this->assertSame($restHandler, $this->restHandlerFactory->createHandler(''));
    }
}
?>