<?php
/**
 * Test for net::stubbles::webapp::xml::route::stubPropertyBasedRouteReader.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::webapp::xml::route::stubPropertyBasedRouteReader');
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::webapp::xml::route::stubPropertyBasedRouteReader.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @since       1.7.0
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_route
 */
class stubPropertyBasedRouteReaderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPropertyBasedRouteReader
     */
    protected $propertyBasedRouteReader;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped(__CLASS__ . '::' . __METHOD__ . ' requires vfsStream, see http://vfs.bovigo.org/.');
        }

        $this->propertyBasedRouteReader = new stubPropertyBasedRouteReader(vfsStream::url('root/pages'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->propertyBasedRouteReader->getClass()
                                                      ->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $this->assertTrue($constructor->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.page.path',
                            $constructor->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function returnsNullForNonExistingRoute()
    {
        vfsStream::setup();
        $this->assertNull($this->propertyBasedRouteReader->getRoute('doesNotExist'));
    }

    /**
     * @test
     */
    public function returnsRouteWithNameAdded()
    {
        $root = vfsStream::setup();
        $conf = vfsStream::newDirectory('pages/conf')->at($root)->getChild('conf');
        vfsStream::newFile('foo.ini')
                 ->at($conf)
                 ->withContent("[properties]\ncached=notFromCache\n[processables]\n");
        $route = $this->propertyBasedRouteReader->getRoute('foo');
        $this->assertEquals('notFromCache',
                            $route->getProperty('cached')
        );
        $this->assertEquals('foo',
                            $route->getProperty('name')
        );
    }
}
?>