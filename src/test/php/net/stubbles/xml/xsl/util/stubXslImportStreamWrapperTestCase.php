<?php
/**
 * Test for net::stubbles::xml::xsl::util::stubXslImportStreamWrapper.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_util_test
 * @version     $Id: stubXslImportStreamWrapperTestCase.php 3220 2011-11-14 15:33:46Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::util::stubXslImportStreamWrapper');
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::xml::xsl::util::stubXslImportStreamWrapper.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_util_test
 * @group       xml
 * @group       xml_xsl
 * @group       xml_xsl_util
 */
class stubXslImportStreamWrapperTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * imports for assertions
     *
     * @var  string
     */
    protected $imports;
    /**
     * config directory
     *
     * @var  vfsStreamDirectory
     */
    protected $configDir;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http://vfs.bovigo.org/');
        }

        libxml_clear_errors();
        $root = vfsStream::setup();
        vfsStream::newDirectory('cache')->at($root);
        $this->configDir = vfsStream::newDirectory('config')->at($root);

        if (DIRECTORY_SEPARATOR == '\\') {
            $this->imports = "<xsl:stylesheet version=\"1.1\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cstubbles.php?xsl/copy.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cstubbles.php?xsl/stub.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cstubbles.php?xsl/ingrid.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cstubbles.php?xsl/variant.xsl\"/>
</xsl:stylesheet>";
        } else {
            $this->imports = "<xsl:stylesheet version=\"1.1\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Fstubbles.php?xsl/copy.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Fstubbles.php?xsl/stub.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Fstubbles.php?xsl/ingrid.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Fstubbles.php?xsl/variant.xsl\"/>
</xsl:stylesheet>";
        }

        if (DIRECTORY_SEPARATOR == '\\') {
            $this->importsExtended = "<xsl:stylesheet version=\"1.1\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cstubbles.php?xsl/copy.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cstubbles.php?xsl/stub.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cstubbles.php?xsl/ingrid.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cstubbles.php?xsl/variant.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%5Clib%5Cfoo.star?xsl/foo.xsl\"/>
  <xsl:import href=\"file://" . urlencode(stubBootstrap::getSourcePath()) . "%5Cresources%5Cxsl/bar.xsl\"/>
</xsl:stylesheet>";
        } else {
            $this->importsExtended = "<xsl:stylesheet version=\"1.1\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Fstubbles.php?xsl/copy.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Fstubbles.php?xsl/stub.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Fstubbles.php?xsl/ingrid.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Fstubbles.php?xsl/variant.xsl\"/>
  <xsl:import href=\"star://" . urlencode(stubBootstrap::getRootPath()) . "%2Flib%2Ffoo.star?xsl/foo.xsl\"/>
  <xsl:import href=\"file://" . urlencode(stubBootstrap::getSourcePath()) . "%2Fresources%2Fxsl/bar.xsl\"/>
</xsl:stylesheet>";
        }
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        libxml_clear_errors();
    }

    /**
     * helper function to initialize the xsl import stream wrapper
     *
     * @param  bool  $enableCache
     */
    protected function initXslImportStreamWrapper($enableCache)
    {
        stubXslImportStreamWrapper::init(vfsStream::url('root/config'), vfsStream::url('root/cache'), $enableCache);
    }

    /**
     * @test
     */
    public function xslimportGeneratedFromEmptyConfigWithCachingDisabledDoesNotGenerateCacheFile()
    {
        vfsStream::newFile('xsl-imports.ini')
                 ->at($this->configDir)
                 ->withContent("[lib]\n\n[resources]\n");
        $this->initXslImportStreamWrapper(false);
        $this->assertEquals($this->imports,
                            file_get_contents('xslimport://master.xsl')
        );
        $this->assertFalse(file_exists(vfsStream::url('root/cache/xsl-imports.cache')));
    }

    /**
     * @test
     */
    public function xslimportGeneratedFromEmptyConfigWithCachingEnabledGeneratesCacheFile()
    {
        vfsStream::newFile('xsl-imports.ini')
                 ->at($this->configDir)
                 ->withContent("[lib]\n\n[resources]\n");
        $this->initXslImportStreamWrapper(true);
        $this->assertEquals($this->imports,
                            file_get_contents('xslimport://master.xsl')
        );
        $this->assertTrue(file_exists(vfsStream::url('root/cache/xsl-imports.cache')));
        $this->assertEquals($this->imports,
                            file_get_contents('xslimport://master.xsl')
        );
    }

    /**
     * @test
     */
    public function xslimportGeneratedFromConfigWithCachingDisabledDoesNotGenerateCacheFile()
    {
        vfsStream::newFile('xsl-imports.ini')
                 ->at($this->configDir)
                 ->withContent("[lib]\ncopy=\"stubbles.php?xsl/copy.xsl\"\nfoo=\"foo.star?xsl/foo.xsl\"\n[resources]\nbar=\"xsl/bar.xsl\"\n");
        $this->initXslImportStreamWrapper(false);
        $this->assertEquals($this->importsExtended,
                            file_get_contents('xslimport://master.xsl')
        );
        $this->assertFalse(file_exists(vfsStream::url('root/cache/xsl-imports.cache')));
    }

    /**
     * @test
     */
    public function xslimportGeneratedFromConfigWithCachingEnabledGeneratesCacheFile()
    {
        vfsStream::newFile('xsl-imports.ini')
                 ->at($this->configDir)
                 ->withContent("[lib]\ncopy=\"stubbles.php?xsl/copy.xsl\"\nfoo=\"foo.star?xsl/foo.xsl\"\n[resources]\nbar=\"xsl/bar.xsl\"\n");
        $this->initXslImportStreamWrapper(true);
        $this->assertEquals($this->importsExtended,
                            file_get_contents('xslimport://master.xsl')
        );
        $this->assertTrue(file_exists(vfsStream::url('root/cache/xsl-imports.cache')));
        $this->assertEquals($this->importsExtended,
                            file_get_contents('xslimport://master.xsl')
        );
    }

    /**
     * @test
     */
    public function configFileDoesNotExistReturnsStylesheetWithDefaultImports()
    {
        $this->initXslImportStreamWrapper(true);
        $this->assertEquals($this->imports,
                            file_get_contents('xslimport://master.xsl')
        );
        $this->assertTrue(file_exists(vfsStream::url('root/cache/xsl-imports.cache')));
        $this->assertEquals($this->imports,
                            file_get_contents(vfsStream::url('root/cache/xsl-imports.cache'))
        );
    }

    /**
     * @test
     */
    public function invalidConfigFiletReturnsStylesheetWithDefaultImports()
    {
        vfsStream::newFile('xsl-imports.ini')
                 ->at($this->configDir)
                 ->withContent("[invalid");
        $this->initXslImportStreamWrapper(true);
        $this->assertEquals($this->imports,
                            file_get_contents('xslimport://master.xsl')
        );
        $this->assertTrue(file_exists(vfsStream::url('root/cache/xsl-imports.cache')));
        $this->assertEquals($this->imports,
                            file_get_contents(vfsStream::url('root/cache/xsl-imports.cache'))
        );
    }

    /**
     * @test
     */
    public function filestatShouldReturnFilesizeEqualToSizeOfReturnedStylesheet()
    {
        vfsStream::newFile('xsl-imports.ini')
                 ->at($this->configDir)
                 ->withContent("[lib]\n\n[resources]\n");
        $this->initXslImportStreamWrapper(false);
        $fp    = fopen('xslimport://master.xsl', 'rb');
        $fstat = fstat($fp);
        $this->assertEquals(filesize('xslimport://master.xsl'),
                            $fstat['size']
        );
        fclose($fp);
    }
}
?>