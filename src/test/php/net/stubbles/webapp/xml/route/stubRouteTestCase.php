<?php
/**
 * Tests for net::stubbles::webapp::xml::route::stubRoute.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route_test
 * @version     $Id: stubRouteTestCase.php 3183 2011-09-05 09:59:31Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::route::stubRoute');
/**
 * Tests for net::stubbles::webapp::xml::route::stubRoute
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_route
 */
class stubRouteTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  stubRoute
     */
    protected $route;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->route = new stubRoute(new stubProperties(array('properties'   => array('stringValue' => 'A string.',
                                                                                      'intValue'    => '303',
                                                                                      'floatValue'  => '3.13',
                                                                                      'boolValue1'  => 'true',
                                                                                      'boolValue2'  => 'false',
                                                                                      'arrayValue'  => 'foo|bar|baz',
                                                                                      'hashValue'   => 'foo:303|bar:313|baz:323',
                                                                                      'rangeValue'  => '1..5'
                                                                                ),
                                                              'processables' => array('foo' => 'org::stubbles::test::FooProcessable',
                                                                                      'bar' => 'org::stubbles::test::BarProcessable'
                                                                                )
                                                        )
                                     )
                       );
    }

    /**
     * @test
     */
    public function hasPropertyReturnsFalseForNonExistingProperty()
    {
        $this->assertFalse($this->route->hasProperty('foo'));
    }

    /**
     * @test
     */
    public function hasPropertyReturnsTrueForExistingProperty()
    {
        $this->assertTrue($this->route->hasProperty('stringValue'));
    }

    /**
     * @test
     */
    public function getPropertyWihoutValueWithoutDefaultReturnsNull()
    {
        $this->assertNull($this->route->getProperty('foo'));
    }

    /**
     * @test
     */
    public function getPropertyWihoutValueWithDefaultReturnsDefault()
    {
        $this->assertEquals('a default',
                            $this->route->getProperty('foo', 'a default')
        );
    }

    /**
     * @test
     */
    public function getPropertyWihValueReturnsValue()
    {
        $this->assertEquals('A string.',
                            $this->route->getProperty('stringValue')
        );
    }

    /**
     * @test
     */
    public function getPropertyWihValueWithDefaultReturnsReturnsValue()
    {
        $this->assertEquals('A string.',
                            $this->route->getProperty('stringValue', 'a default')
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getIntPropertyWihoutValueWithoutDefaultReturnsNull()
    {
        $this->assertEquals(0, $this->route->getPropertyAsInt('foo'));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getIntPropertyWihoutValueWithDefaultReturnsDefault()
    {
        $this->assertEquals(404,
                            $this->route->getPropertyAsInt('foo', 404)
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getIntPropertyWihValueReturnsValue()
    {
        $this->assertEquals(303,
                            $this->route->getPropertyAsInt('intValue')
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getIntPropertyWihValueWithDefaultReturnsReturnsValue()
    {
        $this->assertEquals(303,
                            $this->route->getPropertyAsInt('intValue', 404)
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getFloatPropertyWihoutValueWithoutDefaultReturnsNull()
    {
        $this->assertEquals(0.0, $this->route->getPropertyAsFloat('foo'));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getFloatPropertyWihoutValueWithDefaultReturnsDefault()
    {
        $this->assertEquals(4.04,
                            $this->route->getPropertyAsFloat('foo', 4.04)
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getFloatPropertyWihValueReturnsValue()
    {
        $this->assertEquals(3.13,
                            $this->route->getPropertyAsFloat('floatValue')
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getFloatPropertyWihValueWithDefaultReturnsReturnsValue()
    {
        $this->assertEquals(3.13,
                            $this->route->getPropertyAsFloat('floatValue', 4.04)
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getBoolPropertyWihoutValueWithoutDefaultReturnsFalse()
    {
        $this->assertFalse($this->route->getPropertyAsBool('foo'));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getBoolPropertyWihoutValueWithDefaultReturnsDefault()
    {
        $this->assertTrue($this->route->getPropertyAsBool('foo', true));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getBoolPropertyWihValueReturnsValue()
    {
        $this->assertTrue($this->route->getPropertyAsBool('boolValue1'));
        $this->assertFalse($this->route->getPropertyAsBool('boolValue2'));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getBoolPropertyWihValueWithDefaultReturnsReturnsValue()
    {
        $this->assertTrue($this->route->getPropertyAsBool('boolValue1', false));
        $this->assertFalse($this->route->getPropertyAsBool('boolValue2', true));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getArrayPropertyWihoutValueWithoutDefaultReturnsNull()
    {
        $this->assertNull($this->route->getPropertyAsArray('foo'));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getArrayPropertyWihoutValueWithDefaultReturnsDefault()
    {
        $this->assertEquals(array(404),
                            $this->route->getPropertyAsArray('foo', array(404))
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getArrayPropertyWihValueReturnsValue()
    {
        $this->assertEquals(array('foo', 'bar', 'baz'),
                            $this->route->getPropertyAsArray('arrayValue')
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getArrayPropertyWihValueWithDefaultReturnsReturnsValue()
    {
        $this->assertEquals(array('foo', 'bar', 'baz'),
                            $this->route->getPropertyAsArray('arrayValue', array(404))
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getHashPropertyWihoutValueWithoutDefaultReturnsNull()
    {
        $this->assertNull($this->route->getPropertyAsHash('foo'));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getHashPropertyWihoutValueWithDefaultReturnsDefault()
    {
        $this->assertEquals(array('dummy' => 404),
                            $this->route->getPropertyAsHash('foo', array('dummy' => 404))
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getHashPropertyWihValueReturnsValue()
    {
        $this->assertEquals(array('foo' => 303, 'bar' => 313, 'baz' => 323),
                            $this->route->getPropertyAsHash('hashValue')
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getHashPropertyWihValueWithDefaultReturnsReturnsValue()
    {
        $this->assertEquals(array('foo' => 303, 'bar' => 313, 'baz' => 323),
                            $this->route->getPropertyAsHash('hashValue', array('dummy' => 404))
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getRangePropertyWihoutValueWithoutDefaultReturnsEmptyArray()
    {
        $this->assertEquals(array(), $this->route->getPropertyAsRange('foo'));
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getRangePropertyWihoutValueWithDefaultReturnsDefault()
    {
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'),
                            $this->route->getPropertyAsRange('foo', array('a', 'b', 'c', 'd', 'e'))
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getRangePropertyWihValueReturnsValue()
    {
        $this->assertEquals(array(1, 2, 3, 4, 5),
                            $this->route->getPropertyAsRange('rangeValue')
        );
    }

    /**
     * @test
     * @group  issue272
     * @since  1.7.0
     */
    public function getRangePropertyWihValueWithDefaultReturnsReturnsValue()
    {
        $this->assertEquals(array(1, 2, 3, 4, 5),
                            $this->route->getPropertyAsRange('rangeValue', array('a', 'b', 'c', 'd', 'e'))
        );
    }

    /**
     * @test
     */
    public function listOfProcessablesIsReturned()
    {
        $this->assertEquals(array('foo' => 'org::stubbles::test::FooProcessable',
                                  'bar' => 'org::stubbles::test::BarProcessable'
                            ),
                            $this->route->getProcessables()
        );
    }
}
?>