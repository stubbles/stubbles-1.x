<?php
/**
 * Test for net::stubbles::webapp::xml::route::stubBcRouteReader.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::webapp::xml::route::stubBcRouteReader');
/**
 * Test for net::stubbles::webapp::xml::route::stubBcRouteReader.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @since       1.7.0
 * @deprecated
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_route
 */
class stubBcRouteReaderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function hasAnnotationsOnConstructor()
    {
        $refClass    = new stubReflectionClass('stubBcRouteReader');
        $constructor = $refClass->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[0]->hasAnnotation('Named'));
        $this->assertEquals('xml', $parameters[0]->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function usesRouterToDetectRoute()
    {
        $mockRouter    = $this->getMock('stubRouter');
        $mockRequest   = $this->getMock('stubRequest');
        $bcRouteReader = new stubBcRouteReader($mockRouter, $mockRequest);
        $route         = new stubRoute(new stubProperties());
        $mockRouter->expects($this->once())
                   ->method('route')
                   ->with($this->equalTo($mockRequest))
                   ->will($this->returnValue($route));
        $this->assertSame($route, $bcRouteReader->getRoute('foo'));
    }
}
?>
