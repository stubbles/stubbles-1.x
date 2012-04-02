<?php
/**
 * Tests for net::stubbles::webapp::xml::generator::stubXmlGeneratorFacade.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @version     $Id: stubXmlGeneratorFacadeTestCase.php 3173 2011-08-26 12:18:58Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::generator::stubXmlGeneratorFacade');
/**
 * Tests for net::stubbles::webapp::xml::generator::stubXmlGeneratorFacade.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @since       1.5.0
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_generator
 */
class stubXmlGeneratorFacadeTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXmlGeneratorFacade
     */
    protected $xmlGeneratorFacade;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;
    /**
     * mocked xml stream writer instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXMLStreamWriter;
    /**
     * mocked xml serializer instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXMLSerializer;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest         = $this->getMock('stubRequest');
        $this->mockInjector        = $this->getMock('stubInjector');
        $this->xmlGeneratorFacade  = new stubXmlGeneratorFacade($this->mockRequest, $this->mockInjector);
        $this->mockXMLStreamWriter = $this->getMock('stubXMLStreamWriter');
        $this->mockXMLSerializer   = $this->getMock('stubXMLSerializer', array(), array(), '', false);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->xmlGeneratorFacade->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
    }

    /**
     * prepares generator instances
     *
     * @return  array<PHPUnit_Framework_MockObject_MockObject>
     */
    protected function prepareGenerators()
    {
        $mockedGenerators = array('sessionXmlGenerator' => $this->getMock('stubXmlGenerator'),
                                  'routeXmlGenerator'   => $this->getMock('stubXmlGenerator'),
                                  'requestXmlGenerator' => $this->getMock('stubXmlGenerator')
                            );
        $mockedGenerators['sessionXmlGenerator']->expects($this->once())
                                                ->method('startup');
        $mockedGenerators['routeXmlGenerator']->expects($this->once())
                                              ->method('startup');
        $mockedGenerators['requestXmlGenerator']->expects($this->once())
                                                ->method('startup');
        $this->mockInjector->expects($this->once())
                           ->method('getConstant')
                           ->will($this->returnValue(array('net::stubbles::webapp::xml::generator::stubSessionXmlGenerator',
                                                           'net::stubbles::webapp::xml::generator::stubRouteXmlGenerator',
                                                           'net::stubbles::webapp::xml::generator::stubRequestXmlGenerator'
                                                     )
                                  )
                             );
        $this->mockInjector->expects($this->exactly(3))
                           ->method('getInstance')
                           ->will($this->onConsecutiveCalls($mockedGenerators['sessionXmlGenerator'],
                                                            $mockedGenerators['routeXmlGenerator'],
                                                            $mockedGenerators['requestXmlGenerator']
                                  )
                             );
        $this->xmlGeneratorFacade->startup();
        return $mockedGenerators;
    }

    /**
     * @test
     */
    public function isCachableIfAllGeneratorsAreCachable()
    {
        $mockedGenerators = $this->prepareGenerators();
        $mockedGenerators['sessionXmlGenerator']->expects($this->once())
                                                ->method('isCachable')
                                                ->will($this->returnValue(true));
        $mockedGenerators['routeXmlGenerator']->expects($this->once())
                                              ->method('isCachable')
                                              ->will($this->returnValue(true));
        $mockedGenerators['requestXmlGenerator']->expects($this->once())
                                                ->method('isCachable')
                                                ->will($this->returnValue(true));
        $this->assertTrue($this->xmlGeneratorFacade->isCachable());
    }

    /**
     * @test
     */
    public function isNotCachableIfOneGeneratorIsNotCachable()
    {
        $mockedGenerators = $this->prepareGenerators();
        $mockedGenerators['sessionXmlGenerator']->expects($this->once())
                                                ->method('isCachable')
                                                ->will($this->returnValue(false));
        $mockedGenerators['routeXmlGenerator']->expects($this->never())
                                              ->method('isCachable');
        $mockedGenerators['requestXmlGenerator']->expects($this->never())
                                                ->method('isCachable');
        $this->assertFalse($this->xmlGeneratorFacade->isCachable());
    }

    /**
     * @test
     */
    public function getCacheVarsReturnsCombinedCacheVarsOfAllGenerators()
    {
        $mockedGenerators = $this->prepareGenerators();
        $mockedGenerators['sessionXmlGenerator']->expects($this->once())
                                                ->method('getCacheVars')
                                                ->will($this->returnValue(array('foo' => 'bar')));
        $mockedGenerators['routeXmlGenerator']->expects($this->once())
                                              ->method('getCacheVars')
                                              ->will($this->returnValue(array('bar' => 'baz')));
        $mockedGenerators['requestXmlGenerator']->expects($this->once())
                                                ->method('getCacheVars')
                                                ->will($this->returnValue(array('baz' => 'foo')));
        $this->assertEquals(array('foo' => 'bar',
                                  'bar' => 'baz',
                                  'baz' => 'foo'
                            ),
                            $this->xmlGeneratorFacade->getCacheVars()
        );
    }

    /**
     * @test
     */
   public function generateCallsEachGeneratorIfRequestNotCancelled()
    {
        $this->mockRequest->expects($this->exactly(3))
                          ->method('isCancelled')
                          ->will($this->returnValue(false));
        $mockedGenerators = $this->prepareGenerators();
        $mockedGenerators['sessionXmlGenerator']->expects($this->once())
                                                ->method('generate')
                                                ->with($this->equalTo($this->mockXMLStreamWriter),
                                                       $this->equalTo($this->mockXMLSerializer)
                                                  );
        $mockedGenerators['routeXmlGenerator']->expects($this->once())
                                              ->method('generate')
                                              ->with($this->equalTo($this->mockXMLStreamWriter),
                                                     $this->equalTo($this->mockXMLSerializer)
                                                );
        $mockedGenerators['requestXmlGenerator']->expects($this->once())
                                                ->method('generate')
                                                ->with($this->equalTo($this->mockXMLStreamWriter),
                                                       $this->equalTo($this->mockXMLSerializer)
                                                  );
        $this->xmlGeneratorFacade->generate($this->mockXMLStreamWriter, $this->mockXMLSerializer);
    }

    /**
     * @test
     */
   public function requestCancelledByGeneratorStopsFurtherGenerating()
    {
        $this->mockRequest->expects($this->once())
                          ->method('isCancelled')
                          ->will($this->returnValue(true));
        $mockedGenerators = $this->prepareGenerators();
        $mockedGenerators['sessionXmlGenerator']->expects($this->once())
                                                ->method('generate')
                                                ->with($this->equalTo($this->mockXMLStreamWriter),
                                                       $this->equalTo($this->mockXMLSerializer)
                                                  );
        $mockedGenerators['routeXmlGenerator']->expects($this->never())
                                              ->method('generate');
        $mockedGenerators['requestXmlGenerator']->expects($this->never())
                                                ->method('generate');
        $this->xmlGeneratorFacade->generate($this->mockXMLStreamWriter, $this->mockXMLSerializer);
    }

    /**
     * @test
     */
    public function cleanupIsCalledForEachGenerator()
    {
        $mockedGenerators = $this->prepareGenerators();
        $mockedGenerators['sessionXmlGenerator']->expects($this->once())
                                                ->method('cleanup');
        $mockedGenerators['routeXmlGenerator']->expects($this->once())
                                              ->method('cleanup');
        $mockedGenerators['requestXmlGenerator']->expects($this->once())
                                                ->method('cleanup');
        $this->xmlGeneratorFacade->cleanup();
    }
}
?>
