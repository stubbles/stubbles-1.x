<?php
/**
 * Tests for net::stubbles::websites::xml::skin::stubDefaultSkinGenerator.
 *
 * @package     stubbles
 * @subpackage  websites_xml_skin_test
 * @version     $Id: stubDefaultSkinGeneratorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::xml::skin::stubDefaultSkinGenerator');
/**
 * Tests for net::stubbles::websites::xml::skin::stubDefaultSkinGenerator.
 *
 * @package     stubbles
 * @subpackage  websites_xml_skin_test
 * @group       websites
 * @group       websites_xml
 * @group       websites_xml_skin
 */
class stubDefaultSkinGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDefaultSkinGenerator
     */
    protected $skinGenerator;
    /**
     * mocked xsl processor
     *
     * @var  stubXSLProcessor
     */
    protected $mockXslProcessor;
    /**
     * generated skin dom document
     *
     * @var  DOMDocument
     */
    protected $resultDomDocument;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockXslProcessor  = $this->getMock('stubXSLProcessor',
                                                  array('toDoc')
                                   );
        $this->skinGenerator     = $this->getMock('stubDefaultSkinGenerator',
                                                  array('createXmlSkinDocument',
                                                        'createXslStylesheet'
                                                  ),
                                                  array($this->mockXslProcessor,
                                                        new stubResourceLoader(),
                                                        stubPathRegistry::getCachePath(),
                                                        TEST_SRC_PATH . '/resources/xsl',
                                                        stubPathRegistry::getPagePath()
                                                  )
                                   );
        $this->resultDomDocument = new DOMDocument();
        $this->resultDomDocument->createElement('bar', 'foo');
        $this->mockXslProcessor->expects($this->any())
                               ->method('toDoc')
                               ->will($this->returnValue($this->resultDomDocument));
        $stylesheet = new DOMDocument();
        $stylesheet->loadXML('<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:foo="http://stubbles.net/foo"
    xmlns:ixsl="http://www.w3.org/1999/XSL/TransformOutputAlias">
  <xsl:template match="foo:bar">
    <ixsl:value-of select="/document/Test/foo"/>
  </xsl:template>
</xsl:stylesheet>');
        $this->skinGenerator->expects($this->any())
                            ->method('createXslStylesheet')
                            ->will($this->returnValue($stylesheet));
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $class = new stubReflectionClass('stubDefaultSkinGenerator');
        $this->assertTrue($class->getConstructor()->hasAnnotation('Inject'));

        $params = $class->getConstructor()->getParameters();
        $this->assertTrue($params[2]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.cache.path', $params[2]->getAnnotation('Named')->getName());
        $this->assertTrue($params[3]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.config.path', $params[3]->getAnnotation('Named')->getName());
        $this->assertTrue($params[4]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.page.path', $params[4]->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetFileModeMethod()
    {
        $class = new stubReflectionClass('stubDefaultSkinGenerator');
        $setFileModeMethod = $class->getMethod('setFileMode');
        $this->assertTrue($setFileModeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setFileModeMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setFileModeMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.filemode', $setFileModeMethod->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function annotationsPresentOnEnableCacheMethod()
    {
        $class = new stubReflectionClass('stubDefaultSkinGenerator');
        $enableCacheMethod = $class->getMethod('enableCache');
        $this->assertTrue($enableCacheMethod->hasAnnotation('Inject'));
        $this->assertTrue($enableCacheMethod->getAnnotation('Inject')->isOptional());
    }

    /**
     * @test
     */
    public function annotationsPresentOnEnableCommonPathMethod()
    {
        $class = new stubReflectionClass('stubDefaultSkinGenerator');
        $enableCommonPathMethod = $class->getMethod('enableCommonPath');
        $this->assertTrue($enableCommonPathMethod->hasAnnotation('Inject'));
        $this->assertTrue($enableCommonPathMethod->getAnnotation('Inject')->isOptional());

        $refParams = $enableCommonPathMethod->getParameters();
        $this->assertTrue($refParams[0]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.webapp.xml.skin.common.enable',
                            $refParams[0]->getAnnotation('Named')->getName()
        );
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.page.path.common',
                            $refParams[1]->getAnnotation('Named')->getName()
        );
    }

    /**
     * setting file mode returns instance
     *
     * @test
     */
    public function settingFileModeReturnsInstance()
    {
        $this->assertSame($this->skinGenerator, $this->skinGenerator->setFileMode(0700));
    }

    /**
     * @test
     */
    public function hasSkinReturnsTrueIfSkinIsAvailable()
    {
        $this->assertTrue($this->skinGenerator->hasSkin('default'));
    }

    /**
     * @test
     */
    public function hasSkinReturnsFalseIfSkinIsNotAvailable()
    {
        $this->assertFalse($this->skinGenerator->hasSkin('doesNotExist'));
    }

    /**
     * @test
     */
    public function enableCacheIfRuntimeModeDoesNotDisableCaching()
    {
        $this->assertTrue(stubXslXIncludeStreamWrapper::isCacheEnabled());
        $mockMode = $this->getMock('stubMode');
        $mockMode->expects($this->once())
                 ->method('isCacheEnabled')
                 ->will($this->returnValue(true));
        $this->skinGenerator->enableCache($mockMode);
        $this->skinGenerator->expects($this->any())
                            ->method('createXmlSkinDocument')
                            ->with($this->equalTo('another'))
                            ->will($this->returnValue($this->getMock('DOMDocument')));
        $this->skinGenerator->generate('foo', 'another', 'en_EN', '/');
        $this->assertTrue(stubXslXIncludeStreamWrapper::isCacheEnabled());
    }

    /**
     * @test
     */
    public function disableCacheIfCachingDisabledViaRuntimeMode()
    {
        $this->assertTrue(stubXslXIncludeStreamWrapper::isCacheEnabled());
        $mockMode = $this->getMock('stubMode');
        $mockMode->expects($this->once())
                 ->method('isCacheEnabled')
                 ->will($this->returnValue(false));
        $this->skinGenerator->enableCache($mockMode);
        $this->skinGenerator->expects($this->any())
                            ->method('createXmlSkinDocument')
                            ->with($this->equalTo('another'))
                            ->will($this->returnValue($this->getMock('DOMDocument')));
        $this->skinGenerator->generate('foo', 'another', 'en_EN', '/');
        $this->assertFalse(stubXslXIncludeStreamWrapper::isCacheEnabled());
    }

    /**
     * @test
     */
    public function commonPathIsAddedWhenEnabled()
    {
        $includePathes = stubXslXIncludeStreamWrapper::getIncludePathes();
        $this->assertFalse(isset($includePathes['common']));
        $this->assertSame($this->skinGenerator, $this->skinGenerator->enableCommonPath(true, dirname(__FILE__)));
        $includePathes = stubXslXIncludeStreamWrapper::getIncludePathes();
        $this->assertTrue(isset($includePathes['common']));
        $this->assertEquals(dirname(__FILE__), $includePathes['common']);
    }

    /**
     * @test
     */
    public function createsSkinDocumentBasedOnSkinName()
    {
        $this->skinGenerator->expects($this->once())
                            ->method('createXmlSkinDocument')
                            ->with($this->equalTo('another'))
                            ->will($this->returnValue($this->getMock('DOMDocument')));
        $this->assertSame($this->resultDomDocument, $this->skinGenerator->generate('foo', 'another', 'en_EN', '/'));
    }
}
?>