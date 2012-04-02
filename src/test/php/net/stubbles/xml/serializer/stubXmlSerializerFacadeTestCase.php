<?php
/**
 * Test for net::stubbles::xml::serializer::stubXmlSerializerFacade.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_test
 * @version     $Id: stubXmlSerializerFacadeTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::serializer::stubXmlSerializerFacade');
/**
 * Test for net::stubbles::xml::serializer::stubXmlSerializerFacade.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_test
 * @since       1.1.0
 * @group       xml
 * @group       xml_serializer
 */
class stubXmlSerializerFacadeTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXmlSerializerFacade
     */
    protected $xmlSerializerFacade;
    /**
     * mocked xml serializer
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXmlSerializer;
    /**
     * mocked xml stream writer
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXmlStreamWriter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        libxml_clear_errors();
        $this->mockXmlSerializer   = $this->getMock('stubXMLSerializer', array(), array(), '', false);
        $this->mockXmlStreamWriter = $this->getMock('stubXMLStreamWriter');
        $this->xmlSerializerFacade = new stubXmlSerializerFacade($this->mockXmlSerializer, $this->mockXmlStreamWriter);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        libxml_clear_errors();
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue($this->xmlSerializerFacade->getClass()
                                                    ->getConstructor()
                                                    ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function serializeToXmlReturnsXmlString()
    { 
        $this->mockXmlSerializer->expects($this->once())
                                ->method('serialize')
                                ->with($this->equalTo('foo'), $this->equalTo($this->mockXmlStreamWriter))
                                ->will($this->returnValue($this->mockXmlStreamWriter));
        $this->mockXmlStreamWriter->expects($this->once())
                                  ->method('asXML')
                                  ->will($this->returnValue('<?xml version="1.0" encoding="UTF-8"?><string>foo</string>'));
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><string>foo</string>',
                            $this->xmlSerializerFacade->serializeToXml('foo')
        );
    }

    /**
     * @test
     */
    public function serializeToDomReturnsDOMDocument()
    { 
        $domDocument = new DOMDocument();
        $this->mockXmlSerializer->expects($this->once())
                                ->method('serialize')
                                ->with($this->equalTo('foo'), $this->equalTo($this->mockXmlStreamWriter))
                                ->will($this->returnValue($this->mockXmlStreamWriter));
        $this->mockXmlStreamWriter->expects($this->once())
                                  ->method('asDOM')
                                  ->will($this->returnValue($domDocument));
        $this->assertSame($domDocument, $this->xmlSerializerFacade->serializeToDom('foo'));
    }
}
?>