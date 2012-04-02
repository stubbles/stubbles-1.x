<?php
/**
 * Tests for net::stubbles::webapp::xml::generator::stubRequestXmlGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @version     $Id: stubRequestXmlGeneratorTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::generator::stubRequestXmlGenerator');
/**
 * Tests for net::stubbles::webapp::xml::generator::stubRequestXmlGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_generator
 */
class stubRequestXmlGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRequestXmlGenerator
     */
    protected $requestXMLGenerator;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
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
        libxml_clear_errors();
        $this->mockRequest         = $this->getMock('stubRequest');
        $this->requestXMLGenerator = new stubRequestXmlGenerator($this->mockRequest, new stubUserAgent('foo', false));
        $this->mockXMLStreamWriter = $this->getMock('stubXMLStreamWriter');
        $this->mockXMLSerializer   = $this->getMock('stubXMLSerializer', array(), array(), '', false);
    }

    /**
     * @test
     */
    public function startupDoesNothing()
    {
        $this->requestXMLGenerator->startup();
    }

    /**
     * the request data is always cachable - it is important that the relevant
     * page elements decide about cachability and cache variables
     *
     * @test
     */
    public function cachingMethods()
    {
        $this->assertTrue($this->requestXMLGenerator->isCachable());
        $this->assertEquals(array('isBot' => false), $this->requestXMLGenerator->getCacheVars());
        
    }

    /**
     * @test
     */
    public function noRequestValueErrors()
    {
        $this->mockXMLStreamWriter->expects($this->once())
                                  ->method('writeStartElement')
                                  ->with($this->equalTo('request'));
        $this->mockXMLStreamWriter->expects($this->once())
                                  ->method('writeEndElement');
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $mockRequestValueErrorCollection->expects($this->once())
                                        ->method('get')
                                        ->will($this->returnValue(array()));
        $this->mockRequest->expects($this->once())
                          ->method('paramErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $this->mockXMLSerializer->expects($this->once())->method('serialize');
        $this->requestXMLGenerator->generate($this->mockXMLStreamWriter, $this->mockXMLSerializer);
    }

    /**
     * @test
     */
    public function allRequestValueErrorsForParametersAreSerializedToXml()
    {
        $this->mockXMLStreamWriter->expects($this->at(0))
                                  ->method('writeStartElement')
                                  ->with($this->equalTo('request'));
        $this->mockXMLStreamWriter->expects($this->at(1))
                                  ->method('writeStartElement')
                                  ->with($this->equalTo('value'));
        $this->mockXMLStreamWriter->expects($this->once())
                                  ->method('writeAttribute')
                                  ->with($this->equalTo('name'), $this->equalTo('foo'));
        $this->mockXMLStreamWriter->expects($this->exactly(2))
                                  ->method('writeEndElement');
        $error = new stdClass();
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $mockRequestValueErrorCollection->expects($this->once())
                                        ->method('get')
                                        ->will($this->returnValue(array('foo' => array($error))));
        $this->mockRequest->expects($this->once())
                          ->method('paramErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $this->mockXMLSerializer->expects($this->at(0))
                                ->method('serialize');
        $this->mockXMLSerializer->expects($this->at(1))
                                ->method('serialize')
                                ->with($this->equalTo(array($error)),
                                                      $this->equalTo($this->mockXMLStreamWriter)
                                  );
        $this->requestXMLGenerator->generate($this->mockXMLStreamWriter, $this->mockXMLSerializer);
    }

    /**
     * @test
     */
    public function cleanupDoesNothing()
    {
        $this->requestXMLGenerator->cleanup();
    }
}
?>
