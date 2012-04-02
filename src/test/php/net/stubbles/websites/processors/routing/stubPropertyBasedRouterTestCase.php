<?php
/**
 * Tests for net::stubbles::websites::processors::routing::stubPropertyBasedRouter.
 *
 * @package     stubbles
 * @subpackage  websites_processors_routing_test
 * @version     $Id: stubPropertyBasedRouterTestCase.php 3183 2011-09-05 09:59:31Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubRegexValidator',
                      'net::stubbles::websites::processors::routing::stubPropertyBasedRouter'
);
@include_once 'vfsStream/vfsStream.php';
/**
 * Tests for net::stubbles::websites::processors::routing::stubPropertyBasedRouter
 *
 * @package     stubbles
 * @subpackage  websites_processors_routing_test
 * @since       1.3.0
 * @deprecated
 * @group       websites
 * @group       websites_processors
 * @group       websites_processors_routing
 */
class stubPropertyBasedRouterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest = $this->getMock('stubRequest');
    }

    /**
     * returns an instance were the reroute() method is mocked
     *
     * @return  stubPropertyBasedRouter
     */
    protected function getMockedInstance()
    {
        return $this->getMock('stubPropertyBasedRouter',
                              array('reroute'),
                              array(stubPathRegistry::getPagePath())
               );
    }

    /**
     * returns unmocked instance
     *
     * @param   string                   $routeConfigPath  optional
     * @return  stubPropertyBasedRouter
     */
    protected function getNormalInstance($routeConfigPath = null)
    {
        if (null == $routeConfigPath) {
            $routeConfigPath = stubPathRegistry::getPagePath();
        }

        return new stubPropertyBasedRouter($routeConfigPath);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->getNormalInstance()->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $this->assertTrue($constructor->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.page.path', $constructor->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function noValidRouteRequestValueSetFallsBackToIndexRoute()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->with($this->equalTo('route'))
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'route',
                                                                                  null
                                                    )
                                 )
                            );
        $route = new stubRoute(new stubProperties());
        $propertyBasedRouter = $this->getMockedInstance();
        $propertyBasedRouter->expects($this->once())
                            ->method('reroute')
                            ->with($this->equalTo('index'))
                            ->will($this->returnValue($route));
        $this->assertSame($route, $propertyBasedRouter->route($this->mockRequest));
    }

    /**
     * @test
     */
    public function validRouteRequestValueSetResultsInThisRoute()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->with($this->equalTo('route'))
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'route',
                                                                                  'dummy'
                                                    )
                                 )
                            );
        $route = new stubRoute(new stubProperties());
        $propertyBasedRouter = $this->getMockedInstance();
        $propertyBasedRouter->expects($this->once())
                            ->method('reroute')
                            ->with($this->equalTo('dummy'))
                            ->will($this->returnValue($route));
        $this->assertSame($route, $propertyBasedRouter->route($this->mockRequest));
    }

    /**
     * @test
     */
    public function redirectSetsLocationHeader()
    {
        $mockResponse = $this->getMock('stubResponse');
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->with($this->equalTo('HTTP_HOST'))
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'HTTP_HOST',
                                                                                  'example.net'
                                                    )
                                 )
                            );
        $mockResponse->expects($this->once())
                     ->method('addHeader')
                     ->with($this->equalTo('Location'), $this->equalTo('//example.net/xml/other'));
        $this->getNormalInstance()->redirect($this->mockRequest, $mockResponse, 'other');
    }

    /**
     * @test
     */
    public function routeNameRegexIsSecure()
    {
        $regexValidator = new stubRegexValidator(stubRouter::ROUTENAME_REGEX);
        $this->assertTrue($regexValidator->validate('routename'));
        $this->assertFalse($regexValidator->validate('../routename'));
        $this->assertFalse($regexValidator->validate('routename/..'));
        $this->assertFalse($regexValidator->validate('../../../../../../../../../../../../../../../../../../../../../../../../proc/self/environ%00'));
        $this->assertFalse($regexValidator->validate('environ%00'));
        $this->assertFalse($regexValidator->validate('.svn'));
    }

    /**
     * @test
     */
    public function rerouteToNonExistingRouteReturnsNull()
    {
        $this->assertNull($this->getNormalInstance()->reroute('doesNotExist'));
    }

    /**
     * @test
     */
    public function rerouteReturnsRouteAndCachesRouteIfNoCachedRouteExists()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped(__CLASS__ . '::' . __METHOD__ . ' requires vfsStream, see http://vfs.bovigo.org/.');
        }

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(vfsStream::newDirectory('root'));
        $conf  = vfsStream::newDirectory('pages/conf')->at(vfsStreamWrapper::getRoot())->getChild('conf');
        vfsStream::newFile('foo.ini')->at($conf)->withContent("[properties]\ncached=notFromCache\n[processables]\n");
        $this->assertEquals('foo', $this->getNormalInstance(vfsStream::url('root/pages'))
                                                 ->reroute('foo')
                                                 ->getProperty('name')
        );
    }
}
?>