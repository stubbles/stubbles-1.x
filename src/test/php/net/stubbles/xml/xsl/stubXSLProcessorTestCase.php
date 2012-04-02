<?php
/**
 * Test for net::stubbles::xml::xsl::stubXSLProcessor.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_test
 * @version     $Id: stubXSLProcessorTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::stubXSLProcessor');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_test
 */
class TeststubXSLProcessor extends stubXSLProcessor
{
    /**
     * mocked xslt processor
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    public static $mockXsltProcessor;

    /**
     * return xml document to be transformed
     *
     * @return  DOMDocument
     */
    public function getXmlDocument()
    {
        return $this->document;
    }

    /**
     * overwrite creation method to inject the mock object
     */
    protected function createXsltProcessor()
    {
        $this->xsltProcessor = self::$mockXsltProcessor;
    }
}
/**
 * Test for net::stubbles::xml::xsl::stubXSLProcessor.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_test
 * @group       xml
 * @group       xml_xsl
 */
class stubXSLProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXSLProcessor
     */
    protected $xslProcessor;
    /**
     * a mock for the XSLTProcessor
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXSLTProcessor;
    /**
     * a dom document to test
     *
     * @var  DOMDocument
     */
    protected $document;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('xsl') === false) {
            $this->markTestSkipped('net::stubbles::xml::xsl::stubXSLProcessor requires PHP-extension "xsl".');
        }

        libxml_clear_errors();
        $this->mockXSLTProcessor = $this->getMock('XSLTProcessor');
        TeststubXSLProcessor::$mockXsltProcessor = $this->mockXSLTProcessor;
        $this->xslProcessor = new TeststubXSLProcessor();
        $this->document     = new DOMDocument();
        $this->xslProcessor->onDocument($this->document);
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
    public function providedByXslProcessorProvider()
    {
        $xslProcessor = new stubXSLProcessor();
        $class = $xslProcessor->getClass();
        $this->assertTrue($class->hasAnnotation('ProvidedBy'));
        $this->assertEquals('net::stubbles::xml::xsl::stubXslProcessorProvider',
                            $class->getAnnotation('ProvidedBy')
                                  ->getProviderClass()
                                  ->getFullQualifiedClassName()
        );
    }

    /**
     * @test
     */
    public function onDocumentReturnsItself()
    {
        $this->assertSame($this->xslProcessor, $this->xslProcessor->onDocument($this->document));
    }

    /**
     * @test
     */
    public function onXmlFileReturnsItself()
    {
        $this->assertSame($this->xslProcessor, $this->xslProcessor->onXmlFile(TEST_SRC_PATH . '/resources/xsl/testfile.xsl'));
        $this->assertInstanceOf('DOMDocument', $this->xslProcessor->getXmlDocument());
    }

    /**
     * @test
     * @expectedException  stubIOException
     */
    public function onXMLFileThrowsIoExceptionIfFileDoesNotExist()
    {
        $this->xslProcessor->onXmlFile(TEST_SRC_PATH . '/resources/xsl/doesNotExist.xsl');
    }

    /**
     * @test
     */
    public function applyStylesheetStoresStylesheet()
    {
        $stylesheet = new DOMDocument();
        $this->assertSame($this->xslProcessor, $this->xslProcessor->applyStylesheet($stylesheet));
        $this->assertEquals(array($stylesheet), $this->xslProcessor->getStylesheets());
    }

    /**
     * @test
     */
    public function applyStylesheetFromFileStoresStylesheet()
    {
        $this->assertSame($this->xslProcessor, $this->xslProcessor->applyStylesheetFromFile(TEST_SRC_PATH . '/resources/xsl/testfile.xsl'));
        $this->assertEquals(1, count($this->xslProcessor->getStylesheets()));
    }

    /**
     * @test
     * @expectedException  stubIOException
     */
    public function failingToImportStylesheetFromFileThrowsIOException()
    {
        $this->xslProcessor->applyStylesheetFromFile(TEST_SRC_PATH . '/resources/xsl/doesNotExist.xsl');
    }

    /**
     * test setting and removing single parameters
     *
     * @test
     */
    public function singleParameters()
    {
        $this->mockXSLTProcessor->expects($this->at(0))
                                ->method('setParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo('baz'))
                                ->will($this->returnValue(true));
        $this->mockXSLTProcessor->expects($this->at(1))
                                ->method('setParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('foo'), $this->equalTo('bar'))
                                ->will($this->returnValue(true));
        $this->assertSame($this->xslProcessor, $this->xslProcessor->withParameter('foo', 'bar', 'baz'));
        $this->assertTrue($this->xslProcessor->hasParameter('foo', 'bar'));
        $this->assertEquals('baz', $this->xslProcessor->getParameter('foo', 'bar'));
        $this->assertFalse($this->xslProcessor->hasParameter('foo', 'baz'));
        $this->assertNull($this->xslProcessor->getParameter('foo', 'baz'));
        $this->assertSame($this->xslProcessor, $this->xslProcessor->withParameter('foo', 'foo', 'bar'));
        $this->assertTrue($this->xslProcessor->hasParameter('foo', 'bar'));
        $this->assertTrue($this->xslProcessor->hasParameter('foo', 'foo'));
        $this->assertEquals('bar', $this->xslProcessor->getParameter('foo', 'foo'));
        $this->assertEquals(array('bar' => 'baz', 'foo' => 'bar'), $this->xslProcessor->getParameters('foo'));
        $this->assertEquals(array(), $this->xslProcessor->getParameters('bar'));
        $this->assertEquals(array('foo'), $this->xslProcessor->getParameterNamespaces());
        
        $this->mockXSLTProcessor->expects($this->at(0))
                                ->method('removeParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('bar'))
                                ->will($this->returnValue(false));
        $this->mockXSLTProcessor->expects($this->at(1))
                                ->method('removeParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('bar'))
                                ->will($this->returnValue(true));
        $this->mockXSLTProcessor->expects($this->at(2))
                                ->method('removeParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('foo'))
                                ->will($this->returnValue(true));
        $this->assertFalse($this->xslProcessor->removeParameter('foo', 'bar'));
        $this->assertTrue($this->xslProcessor->hasParameter('foo', 'bar'));
        $this->assertTrue($this->xslProcessor->removeParameter('foo', 'bar'));
        $this->assertFalse($this->xslProcessor->hasParameter('foo', 'bar'));
        $this->assertNull($this->xslProcessor->getParameter('foo', 'bar'));
        $this->assertTrue($this->xslProcessor->removeParameter('foo', 'baz'));
        $this->assertTrue($this->xslProcessor->removeParameter('foo', 'foo'));
        $this->assertNull($this->xslProcessor->getParameter('foo', 'foo'));
        $this->assertEquals(array(), $this->xslProcessor->getParameters('foo'));
        $this->assertEquals(array(), $this->xslProcessor->getParameterNamespaces());
    }

    /**
     * @test
     * @expectedException  stubXSLProcessorException
     */
    public function failingToAddSingleParametersThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('setParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo('baz'))
                                ->will($this->returnValue(false));

        $this->xslProcessor->withParameter('foo', 'bar', 'baz');
    }

    /**
     * test setting and removing array parameters
     *
     * @test
     */
    public function arrayParameters()
    {
        $this->mockXSLTProcessor->expects($this->at(0))
                                ->method('setParameter')
                                ->with($this->equalTo('baz'), $this->equalTo(array('baz' => 'bar')))
                                ->will($this->returnValue(true));
        $this->mockXSLTProcessor->expects($this->at(1))
                                ->method('setParameter')
                                ->with($this->equalTo('baz'), $this->equalTo(array('foo' => 'bar')))
                                ->will($this->returnValue(true));
        $this->assertSame($this->xslProcessor,$this->xslProcessor->withParameters('baz', array('baz' => 'bar')));
        $this->assertSame($this->xslProcessor,$this->xslProcessor->withParameters('baz', array('foo' => 'bar')));
        $this->assertTrue($this->xslProcessor->hasParameter('baz', 'baz'));
        $this->assertTrue($this->xslProcessor->hasParameter('baz', 'foo'));
        $this->assertFalse($this->xslProcessor->hasParameter('baz', 'bar'));
        $this->assertEquals(array('baz' => 'bar', 'foo' => 'bar'), $this->xslProcessor->getParameters('baz'));
        $this->assertEquals(array(), $this->xslProcessor->getParameters('bar'));
        $this->assertEquals(array('baz'), $this->xslProcessor->getParameterNamespaces());
        
        $this->mockXSLTProcessor->expects($this->at(0))
                                ->method('removeParameter')
                                ->with($this->equalTo('baz'), $this->equalTo('foo'))
                                ->will($this->returnValue(false));
        $this->mockXSLTProcessor->expects($this->at(1))
                                ->method('removeParameter')
                                ->with($this->equalTo('baz'), $this->equalTo('baz'))
                                ->will($this->returnValue(true));
        $this->mockXSLTProcessor->expects($this->at(2))
                                ->method('removeParameter')
                                ->with($this->equalTo('baz'), $this->equalTo('foo'))
                                ->will($this->returnValue(true));
        $this->assertEquals(array('foo' => false), $this->xslProcessor->removeParameters('baz', array('foo')));
        $this->assertTrue($this->xslProcessor->hasParameter('baz', 'foo'));
        $this->assertEquals(array('baz' => true), $this->xslProcessor->removeParameters('baz', array('baz')));
        $this->assertFalse($this->xslProcessor->hasParameter('baz', 'bar'));
        $this->assertEquals(array('baz' => true), $this->xslProcessor->removeParameters('baz', array('baz')));
        $this->xslProcessor->removeParameter('baz', 'foo');
        $this->assertEquals(array('baz' => true), $this->xslProcessor->removeParameters('baz', array('baz')));
        $this->assertEquals(array(), $this->xslProcessor->getParameters('baz'));
        $this->assertEquals(array(), $this->xslProcessor->getParameterNamespaces());
    }

    /**
     * @test
     * @expectedException  stubXSLProcessorException
     */
    public function failingToAddListOfParametersThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('setParameter')
                                ->with($this->equalTo('baz'), $this->equalTo(array('bar' => 'baz')))
                                ->will($this->returnValue(false));
        $this->xslProcessor->withParameters('baz', array('bar' => 'baz'));
    }

    /**
     * @test
     */
    public function cloneInstanceCopiesParametersAndStylesheets()
    {
        $anotherMockXSLTProcessor                = $this->getMock('XSLTProcessor');
        TeststubXSLProcessor::$mockXsltProcessor = $anotherMockXSLTProcessor;
        $this->xslProcessor->withParameter('foo', 'bar', 'baz');
        $stylesheet = new DOMDocument();
        $this->xslProcessor->applyStylesheet($stylesheet);
        $this->mockXSLTProcessor->expects($this->never())->method('setParameter');
        $anotherMockXSLTProcessor->expects($this->once())
                                 ->method('setParameter')
                                 ->with($this->equalTo('foo'), $this->equalTo(array('bar' => 'baz')));
        $this->mockXSLTProcessor->expects($this->never())->method('importStylesheet');
        $anotherMockXSLTProcessor->expects($this->once())
                                 ->method('importStylesheet')
                                 ->with($this->equalTo($stylesheet));
        $clonedXSLProcessor = clone $this->xslProcessor;
        $this->assertSame($this->document, $this->xslProcessor->getXmlDocument());
        $this->assertNull($clonedXSLProcessor->getXMLDocument());
    }

    /**
     * @test
     */
    public function transformToDocReturnsDOMDocument()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('transformToDoc')
                                ->with($this->equalTo($this->document))
                                ->will($this->returnValue(new DOMDocument()));
        $this->assertInstanceOf('DOMDocument', $this->xslProcessor->toDoc());
    }

    /**
     * @test
     * @expectedException  stubXSLProcessorException
     */
    public function failingTransformationToDomDocumentThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('transformToDoc')
                                ->will($this->returnValue(false));
        $this->xslProcessor->toDoc();
    }

    /**
     * test transforming a document
     *
     * @test
     */
    public function transformToUri()
    {
        $this->mockXSLTProcessor->expects($this->exactly(2))
                                ->method('transformToUri')
                                ->with($this->equalTo($this->document))
                                ->will($this->onConsecutiveCalls(4555, 0));
        $this->assertEquals(4555, $this->xslProcessor->toURI('foo'));
        $this->assertEquals(0, $this->xslProcessor->toURI('foo'));
    }

    /**
     * @test
     * @expectedException  stubXSLProcessorException
     */
    public function failingTransformationToUriThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('transformToUri')
                                ->with($this->equalTo($this->document))
                                ->will($this->returnValue(false));
        $this->xslProcessor->toURI('foo');
    }

    /**
     * test transforming a document
     *
     * @test
     */
    public function transformToXmlReturnsTransformedXml()
    {
        $this->mockXSLTProcessor->expects($this->exactly(2))
                                ->method('transformToXml')
                                ->with($this->equalTo($this->document))
                                ->will($this->onConsecutiveCalls('<foo>', ''));
        $this->assertEquals('<foo>', $this->xslProcessor->toXML());
        $this->assertEquals('', $this->xslProcessor->toXML());
    }

    /**
     * @test
     * @expectedException  stubXSLProcessorException
     */
    public function failingTransformationToXmlThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->any())
                                ->method('transformToXml')
                                ->with($this->equalTo($this->document))
                                ->will($this->returnValue(false));
        $this->xslProcessor->toXML();
    }

    /**
     * @test
     * @expectedException  stubXslCallbackException
     */
    public function tooLessParamsForCallbackInvocationThrowsCallbackException()
    {
        stubXSLProcessor::invokeCallback();
    }
}
?>