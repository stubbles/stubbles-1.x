<?php
/**
 * Tests for net::stubbles::webapp::xml::generator::stubSessionXmlGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @version     $Id: stubSessionXmlGeneratorTestCase.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::generator::stubSessionXmlGenerator',
                      'net::stubbles::xml::stubXmlStreamWriterProvider'
);
/**
 * Tests for net::stubbles::webapp::xml::generator::stubSessionXmlGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_generator
 */
class stubSessionXmlGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubSessionXmlGenerator
     */
    protected $sessionXMLGenerator;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked session instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
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
        $this->mockRequest         = $this->getMock('stubRequest');
        $this->mockSession         = $this->getMock('stubSession');
        $this->sessionXMLGenerator = new stubSessionXmlGenerator($this->mockRequest, $this->mockSession);
        $provider                  = new stubXmlStreamWriterProvider();
        $this->xmlStreamWriter     = $provider->get();
        $this->mockXMLSerializer   = $this->getMock('stubXMLSerializer', array(), array(), '', false);
    }

    /**
     * startup() does nothing
     *
     * @test
     */
    public function startupDoesNothing()
    {
        $this->sessionXMLGenerator->startup();
    }

    /**
     * cache variables should be whether session is new, the name of the variant
     * and if the requestor accepts cookies or not
     *
     * @test
     */
    public function isCachable()
    {
        $this->assertTrue($this->sessionXMLGenerator->isCachable());
    }

    /**
     * cache variables should be whether session is new, the name of the variant
     * and if the requestor accepts cookies or not
     *
     * @test
     */
    public function cacheVars()
    {
        $this->mockRequest->expects($this->once())->method('acceptsCookies')->will($this->returnValue(true));
        $this->mockSession->expects($this->once())->method('isNew')->will($this->returnValue(true));
        $this->mockSession->expects($this->once())->method('getValue')->will($this->returnValue('variant_name'));
        $this->assertEquals(array('isNew'          => true,
                                  'variant'        => 'variant_name',
                                  'acceptsCookies' => true
                            ),
                            $this->sessionXMLGenerator->getCacheVars()
        );
    }

    /**
     * requestor accepts cookies
     *
     * @test
     */
    public function acceptsCookies()
    {
        $this->mockRequest->expects($this->once())->method('acceptsCookies')->will($this->returnValue(true));
        $this->mockSession->expects($this->once())->method('isNew')->will($this->returnValue(true));
        $this->mockSession->expects($this->exactly(2))->method('getValue')->will($this->onConsecutiveCalls('variant_name', 'variant_alias'));
        $this->sessionXMLGenerator->generate($this->xmlStreamWriter, $this->mockXMLSerializer);
        $doc = $this->xmlStreamWriter->asXML();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
'<session>' . 
  '<acceptsCookies>true</acceptsCookies>' . 
  '<id>$SESSION_ID</id>' . 
  '<name>$SESSION_NAME</name>' . 
  '<isNew>true</isNew>' . 
  '<variant>' . 
    '<name>variant_name</name>' . 
    '<alias>variant_alias</alias>' . 
  '</variant>' . 
'</session>', $doc);
    }

    /**
     * requestor does not accept cookies
     *
     * @test
     */
    public function doesNotAcceptCookies()
    {
        $this->mockRequest->expects($this->once())->method('acceptsCookies')->will($this->returnValue(false));
        $this->mockSession->expects($this->once())->method('isNew')->will($this->returnValue(false));
        $this->mockSession->expects($this->exactly(2))->method('getValue')->will($this->returnValue(null));
        $this->sessionXMLGenerator->generate($this->xmlStreamWriter, $this->mockXMLSerializer);
        $doc = $this->xmlStreamWriter->asXML();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
'<session>' . 
  '<acceptsCookies>false</acceptsCookies>' . 
  '<id>$SESSION_ID</id>' . 
  '<name>$SESSION_NAME</name>' . 
  '<isNew>false</isNew>' . 
  '<variant>' . 
    '<name></name>' . 
    '<alias></alias>' . 
  '</variant>' . 
'</session>', $doc);
    }

    /**
     * cleanup() does nothing
     *
     * @test
     */
    public function cleanupDoesNothing()
    {
        $this->sessionXMLGenerator->cleanup();
    }
}
?>
