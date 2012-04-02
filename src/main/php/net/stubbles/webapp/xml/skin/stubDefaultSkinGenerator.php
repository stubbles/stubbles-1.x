<?php
/**
 * Default implementation to generate the skin to be applied onto the XML result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin
 * @version     $Id: stubDefaultSkinGenerator.php 3198 2011-10-13 13:39:45Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubMode',
                      'net::stubbles::lang::stubResourceLoader',
                      'net::stubbles::webapp::xml::skin::stubSkinGenerator',
                      'net::stubbles::xml::stubXMLException',
                      'net::stubbles::xml::xsl::stubXSLProcessor',
                      'net::stubbles::xml::xsl::util::stubXslImportStreamWrapper',
                      'net::stubbles::xml::xsl::util::stubXslXIncludeStreamWrapper'
);
/**
 * Default implementation to generate the skin to be applied onto the XML result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin
 */
class stubDefaultSkinGenerator extends stubBaseObject implements stubSkinGenerator
{
    /**
     * xsl processor to be used for generating the skin
     *
     * @var  stubXSLProcessor
     */
    protected $xslProcessor;
    /**
     * loader for master.xsl resource file
     *
     * @var  stubResourceLoader
     */
    protected $resourceLoader;
    /**
     * cache path for generated skins
     *
     * @var  string
     */
    protected $cachePath;
    /**
     * config path
     *
     * @var  string
     */
    protected $configPath;
    /**
     * path to page files
     *
     * @var  string
     */
    protected $pagePath;
    /**
     * file mode for cache pathes to create
     *
     * @var  int
     */
    protected $fileMode           = 0700;
    /**
     * switch whether cache is enabled or not
     *
     * @var  bool
     */
    protected $cacheEnabled       = true;

    /**
     * constructor
     *
     * @param  stubXSLProcessor    $xslProcessor
     * @param  stubResourceLoader  $resourceLoader
     * @param  string              $cachePath
     * @param  string              $configPath
     * @param  string              $pagePath
     * @Inject
     * @Named{cachePath}('net.stubbles.cache.path')
     * @Named{configPath}('net.stubbles.config.path')
     * @Named{pagePath}('net.stubbles.page.path')
     */
    public function __construct(stubXSLProcessor $xslProcessor,
                                stubResourceLoader $resourceLoader,
                                $cachePath,
                                $configPath,
                                $pagePath)
    {
        $this->xslProcessor   = $xslProcessor;
        $this->resourceLoader = $resourceLoader;
        $this->cachePath      = $cachePath;
        $this->configPath     = $configPath;
        $this->pagePath       = $pagePath;
    }

    /**
     * sets file mode for cache pathes to create
     *
     * @param   int                       $fileMode
     * @return  stubDefaultSkinGenerator
     * @Inject(optional=true)
     * @Named('net.stubbles.filemode')
     */
    public function setFileMode($fileMode)
    {
        $this->fileMode = $fileMode;
        return $this;
    }

    /**
     * enable/disable caching via mode
     *
     * @param  stubMode  $mode
     * @Inject(optional=true)
     */
    public function enableCache(stubMode $mode)
    {
        $this->cacheEnabled = $mode->isCacheEnabled();
    }

    /**
     * enable common path
     *
     * @param   bool                      $enableCommonPath
     * @param   string                    $commonPagePath
     * @return  stubDefaultSkinGenerator
     * @Inject(optional=true)
     * @Named{enableCommonPath}('net.stubbles.webapp.xml.skin.common.enable')
     * @Named{commonPagePath}('net.stubbles.page.path.common')
     */
    public function enableCommonPath($enableCommonPath, $commonPagePath)
    {
        if (true === (bool) $enableCommonPath) {
            stubXslXIncludeStreamWrapper::addIncludePath('common', $commonPagePath);
        }

        return $this;
    }

    /**
     * checks whether a given skin exists
     *
     * @param   string  $skinName
     * @return  bool
     */
    public function hasSkin($skinName)
    {
        return file_exists($this->pagePath . '/skin/' . $skinName . '.xml');
    }

    /**
     * generates the skin document
     *
     * @param   string       $routeName
     * @param   string       $skinName
     * @param   string       $locale
     * @param   string       $processorUri
     * @return  DOMDocument
     */
    public function generate($routeName, $skinName, $locale, $processorUri)
    {
        stubXslImportStreamWrapper::init($this->configPath, $this->cachePath, $this->cacheEnabled);
        stubXslXIncludeStreamWrapper::register($this->xslProcessor,
                                               $this->pagePath . '/txt',
                                               $this->cachePath . '/xinc',
                                               $this->fileMode,
                                               $this->cacheEnabled,
                                               $routeName
        );
        $resultXSL = $this->xslProcessor->applyStylesheet($this->createXslStylesheet())
                                        ->withParameter('', 'processor_uri', $processorUri)
                                        ->withParameter('', 'page', $routeName)
                                        ->withParameter('', 'lang', $locale)
                                        ->withParameter('', 'lang_base', substr($locale, 0, strpos($locale, '_')) . '_*')
                                        ->onDocument($this->createXmlSkinDocument($skinName))
                                        ->toDoc();
        while (strpos($resultXSL->saveXML(), '<xi:include') !== false) {
            @$resultXSL->xinclude();
        }
        
        return $resultXSL;
    }

    /**
     * creates the xsl stylesheet
     *
     * @return  DOMDocument
     * @todo    fix selection of uri
     */
    protected function createXslStylesheet()
    {
        $uris        = $this->resourceLoader->getResourceUris('xsl/master.xsl');
        $domDocument = new DOMDocument();
        $domDocument->load($uris[0]);
        return $domDocument;
    }

    /**
     * creates the skin document
     *
     * @param   string       $skinName
     * @return  DOMDocument
     * @throws  stubXMLException
     */
    protected function createXmlSkinDocument($skinName)
    {
        $domDocument = new DOMDocument();
        if (file_exists($this->pagePath . '/skin/' . $skinName . '.xml') === false
          || false === $domDocument->load($this->pagePath . '/skin/' . $skinName . '.xml')) {
            throw new stubXMLException('Invalid xml file ' . $this->pagePath . '/skin/' . $skinName . '.xml');
        }
        
        return $domDocument;
    }
}
?>