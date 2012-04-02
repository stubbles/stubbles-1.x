<?php
/**
 * Tests for net::stubbles::webapp::xml::generator::stubModeXmlGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @version     $Id: stubModeXmlGeneratorTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::generator::stubModeXmlGenerator',
                      'net::stubbles::xml::stubXmlStreamWriterProvider'
);
/**
 * Tests for net::stubbles::webapp::xml::generator::stubModeXmlGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_generator
 */
class stubModeXmlGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubModeXmlGenerator
     */
    protected $modeXMLGenerator;
    /**
     * mocked mode instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockMode;
    /**
     * xml stream writer instance
     *
     * @var  stubXMLStreamWrite
     */
    protected $xmlStreamWriter;
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
        $this->mockMode          = $this->getMock('stubMode');
        $this->modeXMLGenerator  = new stubModeXmlGenerator();
        $provider                = new stubXmlStreamWriterProvider();
        $this->xmlStreamWriter   = $provider->get();
        $this->mockXMLSerializer = $this->getMock('stubXMLSerializer', array(), array(), '', false);
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationsPresent()
    {
        $setModeMethod = $this->modeXMLGenerator->getClass()->getMethod('setMode');
        $this->assertTrue($setModeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setModeMethod->getAnnotation('Inject')->isOptional());
    }

    /**
     * startup() does nothing
     *
     * @test
     */
    public function startupsDoesNothing()
    {
        $this->modeXMLGenerator->startup();
    }

    /**
     * cache variables should be whether session is new, the name of the variant
     * and if the requestor accepts cookies or not
     *
     * @test
     */
    public function cacheVars()
    {
        $this->assertEquals(array(), $this->modeXMLGenerator->getCacheVars());
    }

    /**
     * no mode behaves like cachable mode
     *
     * @test
     */
    public function noModeBehavesLikeCachableMode()
    {
        $this->assertTrue($this->modeXMLGenerator->isCachable());
        $this->modeXMLGenerator->generate($this->xmlStreamWriter, $this->mockXMLSerializer);
        $doc = $this->xmlStreamWriter->asXML();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
'<mode>' . 
  '<name>PROD</name>' . 
  '<isCacheEnabled>true</isCacheEnabled>' . 
'</mode>', $doc);
    }

    /**
     * use a cachable mode
     *
     * @test
     */
    public function cachableMode()
    {
        $this->mockMode->expects($this->once())
                       ->method('name')
                       ->will($this->returnValue('PROD'));
        $this->mockMode->expects($this->once())
                       ->method('isCacheEnabled')
                       ->will($this->returnValue(true));
        $this->modeXMLGenerator->setMode($this->mockMode);
        $this->assertTrue($this->modeXMLGenerator->isCachable());
        $this->modeXMLGenerator->generate($this->xmlStreamWriter, $this->mockXMLSerializer);
        $doc = $this->xmlStreamWriter->asXML();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
'<mode>' . 
  '<name>PROD</name>' . 
  '<isCacheEnabled>true</isCacheEnabled>' . 
'</mode>', $doc);
    }

    /**
     * use a non-cachable mode
     *
     * @test
     */
    public function nonCachableMode()
    {
        $this->mockMode->expects($this->once())
                       ->method('name')
                       ->will($this->returnValue('DEV'));
        $this->mockMode->expects($this->once())
                       ->method('isCacheEnabled')
                       ->will($this->returnValue(false));
        $this->modeXMLGenerator->setMode($this->mockMode);
        $this->assertFalse($this->modeXMLGenerator->isCachable());
        $this->modeXMLGenerator->generate($this->xmlStreamWriter, $this->mockXMLSerializer);
        $doc = $this->xmlStreamWriter->asXML();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
'<mode>' . 
  '<name>DEV</name>' . 
  '<isCacheEnabled>false</isCacheEnabled>' . 
'</mode>', $doc);
    }

    /**
     * cleanup() does nothing
     *
     * @test
     */
    public function cleanupDoesNothing()
    {
        $this->modeXMLGenerator->cleanup();
    }
}
?>
