<?php
/**
 * Tests for net::stubbles::webapp::xml::generator::stubRouteXmlGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @version     $Id: stubRouteXmlGeneratorTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::generator::stubRouteXmlGenerator');
/**
 * Tests for net::stubbles::webapp::xml::generator::stubRouteXmlGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_generator
 */
class stubRouteXmlGeneratorTestCase extends PHPUnit_Framework_TestCase
{
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
        $this->mockXMLStreamWriter = $this->getMock('stubXMLStreamWriter');
        $this->mockXMLSerializer   = $this->getMock('stubXMLSerializer', array(), array(), '', false);
    }

    /**
     * creates instance to test
     *
     * @param   array                  $processables
     * @return  stubRouteXMLGenerator
     */
    protected function createRouteXmlGenerator(array $processables)
    {
        return new stubRouteXmlGenerator($this->mockRequest,
                                         $this->mockInjector,
                                         new stubRoute(new stubProperties(array('processables' => $processables)))
        );
    }
    /**
     * route with no processables: nothing to serialize
     *
     * @test
     */
    public function routeWithoutProcessables()
    {
        $this->mockRequest->expects($this->never())->method('isCancelled');
        $this->mockInjector->expects($this->never())->method('getInstance');
        $this->mockXMLSerializer->expects($this->once())
                                ->method('serialize')
                                ->with($this->equalTo(array()), $this->equalTo($this->mockXMLStreamWriter));
        $routeXmlGenerator = $this->createRouteXmlGenerator(array());
        $routeXmlGenerator->startup();
        $this->assertTrue($routeXmlGenerator->isCachable());
        $this->assertEquals(array(), $routeXmlGenerator->getCacheVars());
        $routeXmlGenerator->generate($this->mockXMLStreamWriter, $this->mockXMLSerializer);
    }

    /**
     * route with processables: serialize return values
     *
     * @test
     */
    public function routeWithProcessables()
    {
        $processable1 = $this->getMock('stubProcessable');
        $processable1->expects($this->once())->method('setContext')->will($this->returnValue($processable1));
        $processable1->expects($this->once())->method('startup');
        $processable1->expects($this->once())->method('isAvailable')->will($this->returnValue(true));
        $processable1->expects($this->once())->method('isCachable')->will($this->returnValue(true));
        $processable1->expects($this->once())->method('getCacheVars')->will($this->returnValue(array()));
        $processable1->expects($this->once())->method('process')->will($this->returnValue('foo'));
        $processable2 = $this->getMock('stubProcessable');
        $processable2->expects($this->once())->method('setContext')->will($this->returnValue($processable2));
        $processable2->expects($this->once())->method('startup');
        $processable2->expects($this->once())->method('isAvailable')->will($this->returnValue(false));
        $processable2->expects($this->never())->method('isCachable');
        $processable2->expects($this->never())->method('getCacheVars');
        $processable2->expects($this->never())->method('process');
        $processable3 = $this->getMock('stubXMLFormProcessable');
        $processable3->expects($this->once())->method('setContext')->will($this->returnValue($processable3));
        $processable3->expects($this->once())->method('isAvailable')->will($this->returnValue(true));
        $processable3->expects($this->once())->method('isCachable')->will($this->returnValue(true));
        $processable3->expects($this->once())->method('getCacheVars')->will($this->returnValue(array()));
        $processable3->expects($this->once())->method('process')->will($this->returnValue('baz'));
        $processable3->expects($this->once())->method('getFormValues')->will($this->returnValue(array('foo')));
        
        $this->mockRequest->expects($this->exactly(2))
                          ->method('isCancelled')
                          ->will($this->returnValue(false));
        $this->mockInjector->expects($this->exactly(3))
                           ->method('getInstance')
                           ->will($this->onConsecutiveCalls($processable1, $processable2, $processable3));
        $this->mockXMLSerializer->expects($this->at(0))
                                ->method('serialize')
                                ->with($this->equalTo('foo'), $this->equalTo($this->mockXMLStreamWriter), $this->equalTo('foo'));
        $this->mockXMLSerializer->expects($this->at(1))
                                ->method('serialize')
                                ->with($this->equalTo('baz'), $this->equalTo($this->mockXMLStreamWriter), $this->equalTo('baz'));
        $this->mockXMLSerializer->expects($this->at(2))
                                ->method('serialize')
                                ->with($this->equalTo(array('baz' => array('foo'))), $this->equalTo($this->mockXMLStreamWriter));
        $routeXmlGenerator = $this->createRouteXmlGenerator(array('foo' => get_class($processable1),
                                                                  'bar' => get_class($processable2),
                                                                  'baz' => get_class($processable3)
                                                            )
                             );
        $routeXmlGenerator->startup();
        $this->assertTrue($routeXmlGenerator->isCachable());
        $this->assertEquals(array(), $routeXmlGenerator->getCacheVars());
        $routeXmlGenerator->generate($this->mockXMLStreamWriter, $this->mockXMLSerializer);
    }

    /**
     * route with cancelling processable: stop processing
     *
     * @test
     */
    public function routeWithCancellingProcessable()
    {
        $processable1 = $this->getMock('stubProcessable');
        $processable1->expects($this->once())->method('setContext')->will($this->returnValue($processable1));
        $processable1->expects($this->once())->method('startup');
        $processable1->expects($this->once())->method('isAvailable')->will($this->returnValue(true));
        $processable1->expects($this->once())->method('isCachable')->will($this->returnValue(true));
        $processable1->expects($this->once())->method('getCacheVars')->will($this->returnValue(array('foo' => 'bar')));
        $processable1->expects($this->once())->method('process')->will($this->returnValue('foo'));
        $processable2 = $this->getMock('stubProcessable');
        $processable2->expects($this->once())->method('setContext')->will($this->returnValue($processable2));
        $processable2->expects($this->once())->method('startup');
        $processable2->expects($this->once())->method('isAvailable')->will($this->returnValue(true));
        $processable2->expects($this->once())->method('isCachable')->will($this->returnValue(true));
        $processable2->expects($this->once())->method('getCacheVars')->will($this->returnValue(array('bar' => 313)));
        $processable2->expects($this->never())->method('process');
        
        $this->mockRequest->expects($this->once())
                          ->method('isCancelled')
                          ->will($this->returnValue(true));
        $this->mockInjector->expects($this->exactly(2))
                           ->method('getInstance')
                           ->will($this->onConsecutiveCalls($processable1, $processable2));
        
        $this->mockXMLSerializer->expects($this->never())->method('serialize');
        $routeXmlGenerator = $this->createRouteXmlGenerator(array('foo' => get_class($processable1),
                                                                  'bar' => get_class($processable2)
                                                            )
                             );
        $routeXmlGenerator->startup();
        $this->assertTrue($routeXmlGenerator->isCachable());
        $this->assertEquals(array('foo' => 'bar', 'bar' => 313), $routeXmlGenerator->getCacheVars());
        $routeXmlGenerator->generate($this->mockXMLStreamWriter, $this->mockXMLSerializer);
    }

    /**
     * route with non-cachable processables
     *
     * @test
     */
    public function routeWithNonCachableProcessable()
    {
        $processable1 = $this->getMock('stubProcessable');
        $processable1->expects($this->once())->method('setContext')->will($this->returnValue($processable1));
        $processable1->expects($this->once())->method('startup');
        $processable1->expects($this->once())->method('isAvailable')->will($this->returnValue(true));
        $processable1->expects($this->once())->method('isCachable')->will($this->returnValue(false));
        $processable1->expects($this->never())->method('getCacheVars');
        $processable1->expects($this->once())->method('process')->will($this->returnValue('foo'));
        $processable2 = $this->getMock('stubProcessable');
        $processable2->expects($this->once())->method('setContext')->will($this->returnValue($processable2));
        $processable2->expects($this->once())->method('startup');
        $processable2->expects($this->once())->method('isAvailable')->will($this->returnValue(true));
        $processable2->expects($this->never())->method('isCachable');
        $processable2->expects($this->never())->method('getCacheVars');
        $processable2->expects($this->once())->method('process')->will($this->returnValue('bar'));
        
        $this->mockRequest->expects($this->exactly(2))
                          ->method('isCancelled')
                          ->will($this->returnValue(false));
        $this->mockInjector->expects($this->exactly(2))
                           ->method('getInstance')
                           ->will($this->onConsecutiveCalls($processable1, $processable2));
        
        $this->mockXMLSerializer->expects($this->at(0))
                                ->method('serialize')
                                ->with($this->equalTo('foo'), $this->equalTo($this->mockXMLStreamWriter), $this->equalTo('foo'));
        $this->mockXMLSerializer->expects($this->at(1))
                                ->method('serialize')
                                ->with($this->equalTo('bar'), $this->equalTo($this->mockXMLStreamWriter), $this->equalTo('bar'));
        $routeXmlGenerator = $this->createRouteXmlGenerator(array('foo' => get_class($processable1),
                                                                  'bar' => get_class($processable2)
                                                            )
                             );
        $routeXmlGenerator->startup();
        $this->assertFalse($routeXmlGenerator->isCachable());
        $this->assertEquals(array(), $routeXmlGenerator->getCacheVars());
        $routeXmlGenerator->generate($this->mockXMLStreamWriter, $this->mockXMLSerializer);
    }

    /**
     * cleanup() calls processables' cleanup() method
     *
     * @test
     */
    public function cleanupCallsProcessables()
    {
        $processable1 = $this->getMock('stubProcessable');
        $processable1->expects($this->once())->method('setContext')->will($this->returnValue($processable1));
        $processable1->expects($this->once())->method('startup');
        $processable1->expects($this->once())->method('isAvailable')->will($this->returnValue(true));
        $processable1->expects($this->once())->method('isCachable')->will($this->returnValue(false));
        $processable1->expects($this->once())->method('cleanup')->will($this->returnValue(true));
        $processable2 = $this->getMock('stubProcessable');
        $processable2->expects($this->once())->method('setContext')->will($this->returnValue($processable2));
        $processable2->expects($this->once())->method('startup');
        $processable1->expects($this->once())->method('isAvailable')->will($this->returnValue(false));
        $processable2->expects($this->never())->method('cleanup');
        $this->mockInjector->expects($this->exactly(2))
                           ->method('getInstance')
                           ->will($this->onConsecutiveCalls($processable1, $processable2));

        $routeXmlGenerator = $this->createRouteXmlGenerator(array('foo' => get_class($processable1),
                                                                  'bar' => get_class($processable2)
                                                            )
                             );
        $routeXmlGenerator->startup();
        $routeXmlGenerator->cleanup();
    }
}
?>