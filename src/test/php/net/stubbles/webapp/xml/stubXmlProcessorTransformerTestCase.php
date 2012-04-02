<?php
/**
 * Test for net::stubbles::webapp::xml::stubXmlProcessorTransformer.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_test
 * @version     $Id: stubXmlProcessorTransformerTestCase.php 3239 2011-11-30 08:53:36Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::stubXmlProcessorTransformer');
/**
 * Test for net::stubbles::webapp::xml::stubXmlProcessorTransformer.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_test
 * @since       1.5.0
 * @group       webapp
 * @group       webapp_xml
 */
class stubXmlProcessorTransformerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXmlProcessorTransformer
     */
    protected $xmlProcessorTransformer;
    /**
     * mocked xsl processor
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXslProcessor;
    /**
     * mocked skin generator
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSkinGenerator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockXslProcessor  = $this->getMock('stubXSLProcessor');
        $this->mockSkinGenerator = $this->getMock('stubSkinGenerator');
        $this->xmlProcessorTransformer = new stubXmlProcessorTransformer($this->mockXslProcessor,
                                                                         $this->mockSkinGenerator,
                                                                         'foo_FOO'
                                         );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->xmlProcessorTransformer->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $refParams = $constructor->getParameters();
        $this->assertTrue($refParams[2]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.locale',
                            $refParams[2]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function noSkinSelectedUsesDefaultSkin()
    {
        $this->assertEquals('default', $this->xmlProcessorTransformer->getSelectedSkinName());
    }

    /**
     * @test
     */
    public function selectSkinFromRequestIfIsValid()
    {
        $this->mockSkinGenerator->expects($this->once())
                                ->method('hasSkin')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(true));
        $this->assertSame($this->xmlProcessorTransformer,
                          $this->xmlProcessorTransformer->selectSkin('foo', 'bar')
        );
        $this->assertEquals('foo', $this->xmlProcessorTransformer->getSelectedSkinName());
    }

    /**
     * @test
     */
    public function selectSkinFromRouteIfRequestSkinIsNotValid()
    {
        $this->mockSkinGenerator->expects($this->at(0))
                                ->method('hasSkin')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(false));
        $this->mockSkinGenerator->expects($this->at(1))
                                ->method('hasSkin')
                                ->with($this->equalTo('bar'))
                                ->will($this->returnValue(true));
        $this->assertSame($this->xmlProcessorTransformer,
                          $this->xmlProcessorTransformer->selectSkin('foo', 'bar')
        );
        $this->assertEquals('bar', $this->xmlProcessorTransformer->getSelectedSkinName());
    }

    /**
     * @test
     */
    public function selectSkinFromRouteIfRequestSkinIsEmptyAndRouteSkinIsValid()
    {
        $this->mockSkinGenerator->expects($this->once())
                                ->method('hasSkin')
                                ->with($this->equalTo('bar'))
                                ->will($this->returnValue(true));
        $this->assertSame($this->xmlProcessorTransformer,
                          $this->xmlProcessorTransformer->selectSkin(null, 'bar')
        );
        $this->assertEquals('bar', $this->xmlProcessorTransformer->getSelectedSkinName());
    }

    /**
     * @test
     */
    public function selectDefaultSkinIfRequestAndRouteSkinNotValid()
    {
        $this->mockSkinGenerator->expects($this->at(0))
                                ->method('hasSkin')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(false));
        $this->mockSkinGenerator->expects($this->at(1))
                                ->method('hasSkin')
                                ->with($this->equalTo('bar'))
                                ->will($this->returnValue(false));
        $this->assertSame($this->xmlProcessorTransformer,
                          $this->xmlProcessorTransformer->selectSkin('foo', 'bar')
        );
        $this->assertEquals('default', $this->xmlProcessorTransformer->getSelectedSkinName());
    }

    /**
     * @test
     */
    public function selectDefaultSkinIfRequestAndRouteSkinBothEmpty()
    {
        $this->mockSkinGenerator->expects($this->never())
                                ->method('hasSkin');
        $this->assertSame($this->xmlProcessorTransformer,
                          $this->xmlProcessorTransformer->selectSkin('', '')
        );
        $this->assertEquals('default', $this->xmlProcessorTransformer->getSelectedSkinName());
    }

    /**
     * @test
     */
    public function selectRequestLocaleIfNotEmpty()
    {
        $this->assertSame($this->xmlProcessorTransformer,
                          $this->xmlProcessorTransformer->selectLocale('de_DE', 'en_EN')
        );
        $this->assertEquals('de_DE', $this->xmlProcessorTransformer->getSelectedLocale());
    }

    /**
     * @test
     */
    public function selectRouteLocaleIfRequestLocaleEmptyAndRouteLocaleNotEmpty()
    {
        $this->assertSame($this->xmlProcessorTransformer,
                          $this->xmlProcessorTransformer->selectLocale('', 'en_EN')
        );
        $this->assertEquals('en_EN', $this->xmlProcessorTransformer->getSelectedLocale());
    }

    /**
     * @test
     */
    public function selectOriginalLocaleIfRequestLocaleAndRouteLocaleEmpty()
    {
        $this->assertSame($this->xmlProcessorTransformer,
                          $this->xmlProcessorTransformer->selectLocale('', '')
        );
        $this->assertEquals('foo_FOO', $this->xmlProcessorTransformer->getSelectedLocale());
    }

    /**
     * @test
     */
    public function transformTransformsStreamWriterContentUsingSkinGenerator()
    {
        $skinGeneratorResult = new DOMDocument();
        $this->mockSkinGenerator->expects($this->once())
                                ->method('generate')
                                ->with($this->equalTo('index'),
                                       $this->equalTo('default'),
                                       $this->equalTo('foo_FOO'),
                                       $this->equalTo('/')
                                  )
                                ->will($this->returnValue($skinGeneratorResult));
        $xmlStreamWriterResult = new DOMDocument();
        $xmlStreamWriter       = $this->getMock('stubXMLStreamWriter');
        $xmlStreamWriter->expects($this->once())
                        ->method('asDOM')
                        ->will($this->returnValue($xmlStreamWriterResult));
        $this->mockXslProcessor->expects($this->never())
                               ->method('enableProfiling');
        $this->mockXslProcessor->expects($this->once())
                               ->method('applyStylesheet')
                               ->with($this->equalTo($skinGeneratorResult))
                               ->will($this->returnValue($this->mockXslProcessor));
        $this->mockXslProcessor->expects($this->once())
                               ->method('onDocument')
                               ->with($this->equalTo($xmlStreamWriterResult))
                               ->will($this->returnValue($this->mockXslProcessor));
        $this->mockXslProcessor->expects($this->once())
                               ->method('toXml')
                               ->will($this->returnValue('<html xmlns=""><head><title xml:base="foo">Test</title></head><body><p>Hello world.</p></body></html>'));
        $this->assertEquals('<html><head><title>Test</title></head><body><p>Hello world.</p></body></html>',
                            $this->xmlProcessorTransformer->transform($xmlStreamWriter, 'index')
        );
    }
}
?>