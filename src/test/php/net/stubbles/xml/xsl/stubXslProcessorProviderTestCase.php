<?php
/**
 * Test for net::stubbles::xml::xsl::stubXslProcessorProvider.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_test
 * @version     $Id: stubXslProcessorProviderTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::stubXslProcessorProvider');
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::xml::xsl::stubXslProcessorProvider.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_test
 * @since       1.5.0
 * @group       xml
 * @group       xml_xsl
 */
class stubXslProcessorProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXslProcessorProvider.
     */
    protected $xslProcessorProvider;
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
        $this->mockInjector         = $this->getMock('stubInjector');
        if (class_exists('vfsStream', false) === true) {
            vfsStream::setup();
            $configPath = vfsStream::url('root');
        } else {
            $configPath = dirname(__FILE__);
        }

        $this->xslProcessorProvider = new stubXslProcessorProvider($this->mockInjector, $configPath);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->xslProcessorProvider->getClass()->getConstructor();

        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $refParams = $constructor->getParameters();
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.config.path',
                            $refParams[1]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function createXslProcessorWithoutCallbacks()
    {
        $xslProcessor = $this->xslProcessorProvider->get('net.stubbles.xml.xsl.callbacks.disabled');
        $this->assertInstanceOf('stubXSLProcessor', $xslProcessor);
        $this->assertEquals(array(), $xslProcessor->getCallbacks());
    }

    /**
     * @test
     */
    public function createWithNonExistingCallbackConfigurationReturnsXslProcessorWithoutCallbacks()
    {
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $xslProcessor = $this->xslProcessorProvider->get();
        $this->assertInstanceOf('stubXSLProcessor', $xslProcessor);
        $this->assertEquals(array(), $xslProcessor->getCallbacks());
    }

    /**
     * @test
     * @expectedException  stubConfigurationException
     */
    public function createWithInvalidCallbackConfigurationThrowsConfigurationException()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Test requires vfsStream, see http://vfs.bovigo.org');
        }

        if (version_compare(phpversion(), '5.3.0', '<') === true) {
            $this->markTestSkipped('Can not force parse error with PHP < 5.3');
        }

        vfsStream::newFile('xsl-callbacks.ini')
                 ->withContent('!')
                 ->at(vfsStreamWrapper::getRoot());
        $xslProcessor = $this->xslProcessorProvider->get();
    }

    /**
     * @test
     */
    public function createWithCallbacksReturnsXslProcessorWithCallbacks()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Test requires vfsStream, see http://vfs.bovigo.org');
        }

        vfsStream::newFile('xsl-callbacks.ini')
                 ->withContent('foo="org::stubbles::example::xsl::ExampleCallback"')
                 ->at(vfsStreamWrapper::getRoot());
        $mockCallback = $this->getMock('stubObject');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::example::xsl::ExampleCallback'))
                           ->will($this->returnValue($mockCallback));
        $xslProcessor = $this->xslProcessorProvider->get();
        $this->assertInstanceOf('stubXSLProcessor', $xslProcessor);
        $this->assertEquals(array('foo' => $mockCallback),
                            $xslProcessor->getCallbacks()
        );
    }
}
?>
