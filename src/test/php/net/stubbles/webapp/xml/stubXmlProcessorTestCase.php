<?php
/**
 * Tests for net::stubbles::webapp::xml::stubXmlProcessor.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_test
 * @version     $Id: stubXmlProcessorTestCase.php 3210 2011-11-10 20:54:17Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::stubXmlProcessor');
/**
 * Tests for net::stubbles::webapp::xml::stubXmlProcessor.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_test
 * @group       webapp
 * @group       webapp_xml
 */
class stubXmlProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  stubXmlProcessor
     */
    protected $xmlProcessor;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked session instance
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
     * mocked generator facade
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXmlGeneratorFacade;
    /**
     * mocked transformer instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXmlProcessorTransformer;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;
    /**
     * mocked route reader instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRouteReader;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest = $this->getMock('stubRequest');
        $this->mockRequest->expects($this->any())
                          ->method('getValueErrors')
                          ->will($this->returnValue(array()));
        $this->mockSession = $this->getMock('stubSession');
        $this->mockResponse    = $this->getMock('stubResponse');
        $this->mockRouteReader = $this->getMock('stubRouteReader');
        $this->mockXmlGeneratorFacade      = $this->getMock('stubXmlGeneratorFacade',
                                                            array(),
                                                            array($this->getMock('stubRequest'),
                                                                  $this->getMock('stubInjector')
                                                            )
                                             );
        $this->mockXmlProcessorTransformer = $this->getMock('stubXmlProcessorTransformer',
                                                            array(),
                                                            array($this->getMock('stubXSLProcessor'),
                                                                  $this->getMock('stubSkinGenerator'),
                                                                  'en_EN'
                                                            )
                                             );
        $this->mockInjector                = $this->getMock('stubInjector');
        $this->xmlProcessor                = new stubXmlProcessor($this->mockRequest,
                                                                  $this->mockSession,
                                                                  $this->mockResponse,
                                                                  $this->mockRouteReader,
                                                                  $this->mockXmlGeneratorFacade,
                                                                  $this->mockXmlProcessorTransformer,
                                                                  $this->mockInjector
                                             );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->xmlProcessor->getClass()
                                             ->getConstructor()
                                             ->hasAnnotation('Inject')
        );
    }

    /**
     * creates route with given properties
     *
     * @param   array      $properties  optional
     * @return  stubRoute
     */
    protected function createRoute(array $properties = array())
    {
        if (isset($properties['name']) === false) {
            $properties['name'] = 'Home';
        }
        
        return new stubRoute(new stubProperties(array('properties' => $properties)));
    }

    /**
     * finished mocked route reader
     *
     * @param  stubRoute  $route     optional
     * @param  stubRoute  $fallback  optional
     */
    protected function setRouteForMockRouteReader(stubRoute $route = null, stubRoute $fallback = null)
    {
        $this->mockRouteReader->expects($this->any())
                              ->method('getRoute')
                              ->will($this->onConsecutiveCalls($route, $fallback));
        if (null === $route && null === $fallback) {
            return;
        }

        $this->mockSession->expects($this->at(0))
                          ->method('putValue')
                          ->with($this->equalTo('net.stubbles.webapp.lastPage'),
                                 $this->equalTo('Home')
                            );
        $this->mockInjector->expects($this->once())
                           ->method('bind')
                           ->with($this->equalTo('stubRoute'))
                           ->will($this->returnValue($this->getMock('stubClassBinding', array(), array(), '', false)));
        $this->mockRequest->expects($this->any())
                          ->method('readParam')
                          ->with($this->equalTo('frame'))
                          ->will($this->returnValue(new stubMockFilteringRequestValue('frame', '')));
        $this->mockXmlProcessorTransformer->expects($this->any())
                                          ->method('selectSkin')
                                          ->will($this->returnValue($this->mockXmlProcessorTransformer));
        $this->mockXmlProcessorTransformer->expects($this->any())
                                          ->method('selectLocale')
                                          ->will($this->returnValue($this->mockXmlProcessorTransformer));

    }

    /**
     * @test
     * @expectedException  stubProcessorException
     */
    public function throwsProcessorExceptionIfRouteDoesNotExistAndNoFallbackAvailable()
    {
        $this->setRouteForMockRouteReader(null);
        $this->mockRequest->expects($this->once())
                          ->method('cancel');
        $this->xmlProcessor->startup(new stubUriRequest('/xml/doesNotExist'));
    }

    /**
     * @test
     */
    public function setsStatusCodeTo404IfRouteDoesNotExistButFallbackAvailable()
    {
        $this->setRouteForMockRouteReader(null, $this->createRoute());
        $this->mockRequest->expects($this->never())
                          ->method('cancel');
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo(404));
        $this->xmlProcessor->startup(new stubUriRequest('/xml/doesNotExist'));
    }

    /**
     * @test
     */
    public function returnsDefaultRoleIfNoRoleSpecified()
    {
        $this->setRouteForMockRouteReader($this->createRoute());
        $this->assertEquals('default',
                            $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'))
                                               ->getRequiredRole('default'));
    }

    /**
     * @test
     */
    public function returnsRoleIfRoleSpecified()
    {
        $this->setRouteForMockRouteReader($this->createRoute(array('role' => 'admin')));
        $this->assertEquals('admin',
                            $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'))
                                               ->getRequiredRole('default')
        );
    }

    /**
     * @test
     */
    public function doesNotForceSslIfRouteDoesNotHaveForceSslProperty()
    {
        $this->setRouteForMockRouteReader($this->createRoute());
        $this->assertFalse($this->xmlProcessor->startup(new stubUriRequest('/xml/Home'))->forceSsl());
    }

    /**
     * @test
     */
    public function doesNotForceSslIfRouteForceSslPropertyNotSetTo()
    {
        $this->setRouteForMockRouteReader($this->createRoute());
        $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'));
        $this->assertFalse($this->xmlProcessor->forceSsl());
    }

    /**
     * @test
     */
    public function doesForceSslIfRouteForceSslPropertySetToTrue()
    {
        $this->setRouteForMockRouteReader($this->createRoute(array('forceSsl' => 'true')));
        $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'));
        $this->assertTrue($this->xmlProcessor->forceSsl());
    }

    /**
     * @test
     */
    public function startupSelectsSkinAndLocale()
    {
        $this->setRouteForMockRouteReader($this->createRoute());
        $this->mockXmlGeneratorFacade->expects($this->once())
                                     ->method('startup');
        $this->assertSame($this->xmlProcessor,
                          $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'))
        );
    }

    /**
     * @test
     */
    public function isCachableIfGeneratorsAreCachable()
    {
        $this->mockXmlGeneratorFacade->expects($this->once())
                                     ->method('isCachable')
                                     ->will($this->returnValue(true));
        $this->assertTrue($this->xmlProcessor->isCachable());
    }

    /**
     * @test
     */
    public function isNotCachableIfGeneratorsAreNotCachable()
    {
        $this->mockXmlGeneratorFacade->expects($this->once())
                                     ->method('isCachable')
                                     ->will($this->returnValue(false));
        $this->assertFalse($this->xmlProcessor->isCachable());
    }

    /**
     * @test
     */
    public function getCacheVarsReturnsCombinedCacheVarsOfProcessorAndGenerators()
    {
        $this->setRouteForMockRouteReader($this->createRoute());
        $this->mockXmlGeneratorFacade->expects($this->once())
                                     ->method('getCacheVars')
                                     ->will($this->returnValue(array('foo' => 'bar',
                                                                     'bar' => 'baz',
                                                                     'baz' => 'foo'
                                                               )
                                            )
                                       );
        $this->mockXmlProcessorTransformer->expects($this->once())
                                          ->method('getSelectedSkinName')
                                          ->will($this->returnValue('default'));
        $this->mockXmlProcessorTransformer->expects($this->once())
                                          ->method('getSelectedLocale')
                                          ->will($this->returnValue('en_EN'));
        $this->assertEquals(array('foo'      => 'bar',
                                  'bar'      => 'baz',
                                  'baz'      => 'foo',
                                  'route'    => 'Home',
                                  'skin'     => 'default',
                                  'locale'   => 'en_EN'
                            ),
                            $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'))
                                               ->getCacheVars()
        );
    }

    /**
     * @test
     */
    public function routeNameIsNameOfSelectedRoute()
    {
        $this->setRouteForMockRouteReader($this->createRoute());
        $this->assertEquals('Home',
                            $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'))
                                               ->getRouteName()
        );
    }

    /**
     * @test
     */
    public function processFillsResponseIfNotCancelledByGenerators()
    {
        $this->setRouteForMockRouteReader($this->createRoute());
        $this->mockRequest->expects($this->once())
                          ->method('isCancelled')
                          ->will($this->returnValue(false));
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlSerializer   = $this->getMock('stubXMLSerializer', array(), array(), '', false);
        $this->mockInjector->expects($this->exactly(2))
                           ->method('getInstance')
                           ->will($this->onConsecutiveCalls($mockXmlStreamWriter, $mockXmlSerializer));
        $this->mockXmlGeneratorFacade->expects($this->once())
                                     ->method('generate')
                                     ->with($this->equalTo($mockXmlStreamWriter),
                                            $this->equalTo($mockXmlSerializer)
                                       );
        $mockXmlStreamWriter->expects($this->once())
                            ->method('writeStartElement');
        $mockXmlStreamWriter->expects($this->once())
                            ->method('writeAttribute')
                            ->with($this->equalTo('page'), $this->equalTo('Home'));
        $mockXmlStreamWriter->expects($this->once())
                            ->method('writeEndElement');
        $mockXmlStreamWriter->expects($this->any())
                            ->method('asXML')
                            ->will($this->returnValue('<bar>foo</bar>'));
        $this->mockXmlProcessorTransformer->expects($this->once())
                                          ->method('transform')
                                          ->will($this->returnValue('<html><head><title>Test</title></head><body><p>Hello world.</p></body></html>'));
        $this->mockSession->expects($this->at(2))
                          ->method('putValue')
                          ->with($this->equalTo('net.stubbles.webapp.lastRequestResponseData'), $this->equalTo('<bar>foo</bar>'));
        $this->mockResponse->expects($this->once())
                          ->method('replaceBody')
                          ->with($this->equalTo('<html><head><title>Test</title></head><body><p>Hello world.</p></body></html>'));
        $this->assertSame($this->xmlProcessor,
                          $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'))
                                             ->process()
        );
    }

    /**
     * @test
     */
    public function processLeavesResponseEmptyIfRequestCancelledByGenerators()
    {
        $this->setRouteForMockRouteReader($this->createRoute());
        $this->mockRequest->expects($this->once())
                          ->method('isCancelled')
                          ->will($this->returnValue(true));
        $mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $mockXmlSerializer   = $this->getMock('stubXMLSerializer', array(), array(), '', false);
        $this->mockInjector->expects($this->exactly(2))
                           ->method('getInstance')
                           ->will($this->onConsecutiveCalls($mockXmlStreamWriter, $mockXmlSerializer));
        $this->mockXmlGeneratorFacade->expects($this->once())
                                     ->method('generate')
                                     ->with($this->equalTo($mockXmlStreamWriter),
                                            $this->equalTo($mockXmlSerializer)
                                       );
        $mockXmlStreamWriter->expects($this->never())->method('asXML');
        $this->mockXmlProcessorTransformer->expects($this->never())
                                          ->method('transform');
        $this->mockResponse->expects($this->never())->method('replaceBody');
        $this->assertSame($this->xmlProcessor,
                          $this->xmlProcessor->startup(new stubUriRequest('/xml/Home'))
                                             ->process()
        );
    }

    /**
     * @test
     */
    public function cleanupCallsCleanupOfGenerators()
    {
        $this->mockXmlGeneratorFacade->expects($this->once())
                                     ->method('cleanup');
        $this->assertSame($this->xmlProcessor, $this->xmlProcessor->cleanup());
    }
}
?>