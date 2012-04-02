<?php
/**
 * Test for net::stubbles::xml::stubXmlStreamWriterProvider.
 *
 * @package     stubbles
 * @subpackage  xml_test
 * @version     $Id: stubXmlStreamWriterProviderTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubXmlStreamWriterProvider',
                      'net::stubbles::xml::stubXMLStreamWriter'
);
/**
 * Test for net::stubbles::xml::stubXmlStreamWriterProvider.
 *
 * @package     stubbles
 * @subpackage  xml_test
 * @group       xml
 */
class stubXmlStreamWriterProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXmlStreamWriterProvider
     */
    protected $xmlStreamWriterProvider;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->xmlStreamWriterProvider = new stubXmlStreamWriterProvider();
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $class  = $this->xmlStreamWriterProvider->getClass();
        $method = $class->getMethod('setTypes');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.xml.types', $method->getAnnotation('Named')->getName());
        
        $method = $class->getMethod('setVersion');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.xml.version', $method->getAnnotation('Named')->getName());
        
        $method = $class->getMethod('setEncoding');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.xml.encoding', $method->getAnnotation('Named')->getName());
        
        $class = new stubReflectionClass('net::stubbles::xml::stubXMLStreamWriter');
        $this->assertTrue($class->hasAnnotation('ProvidedBy'));
        $this->assertEquals('net::stubbles::xml::stubXmlStreamWriterProvider',
                            $class->getAnnotation('ProvidedBy')
                                  ->getProviderClass()
                                  ->getFullQualifiedClassName()
        );
    }

    /**
     * @test
     */
    public function noSpecificRequestedTypeShouldCreateFirstAvailableType()
    {
        if (extension_loaded('dom') === true) {
            $this->assertInstanceOf('stubDomXMLStreamWriter', $this->xmlStreamWriterProvider->get());
        } elseif (extension_loaded('xmlwriter') === true) {
            $this->assertInstanceOf('stubLibXmlXMLStreamWriter', $this->xmlStreamWriterProvider->get());
        }
    }

    /**
     * @test
     * @expectedException  stubXMLException
     */
    public function noTypeAvailableThrowsException()
    {
        $this->xmlStreamWriterProvider->setTypes(array());
        $this->xmlStreamWriterProvider->get();
    }

    /**
     * @test
     */
    public function createDomTypeIfRequested()
    {
        $this->assertInstanceOf('stubDomXMLStreamWriter', $this->xmlStreamWriterProvider->get('dom'));
    }

    /**
     * @test
     */
    public function createXmlWriterTypeIfRequested()
    {
        $this->assertInstanceOf('stubLibXmlXMLStreamWriter', $this->xmlStreamWriterProvider->get('xmlwriter'));
    }

    /**
     * @test
     */
    public function setVersion()
    {
        $writer = $this->xmlStreamWriterProvider->get();
        $this->assertEquals('1.0', $writer->getVersion());
        $this->xmlStreamWriterProvider->setVersion('1.1');
        $writer = $this->xmlStreamWriterProvider->get();
        $this->assertEquals('1.1', $writer->getVersion());
    }

    /**
     * @test
     */
    public function setEncoding()
    {
        $writer = $this->xmlStreamWriterProvider->get();
        $this->assertEquals('UTF-8', $writer->getEncoding());
        $this->xmlStreamWriterProvider->setEncoding('ISO-8859-1');
        $writer = $this->xmlStreamWriterProvider->get();
        $this->assertEquals('ISO-8859-1', $writer->getEncoding());
    }
}
?>