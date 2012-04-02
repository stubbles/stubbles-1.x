<?php
/**
 * Transforms the xml stream from the xml processor into desired output format.
 *
 * @package     stubbles
 * @subpackage  webapp_xml
 * @version     $Id: stubXmlProcessorTransformer.php 3234 2011-11-29 15:51:57Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::skin::stubSkinGenerator',
                      'net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::xsl::stubXSLProcessor'
);
/**
 * Transforms the xml stream from the xml processor into desired output format.
 *
 * @package     stubbles
 * @subpackage  webapp_xml
 * @since       1.5.0
 */
class stubXmlProcessorTransformer extends stubBaseObject
{
    /**
     * skin generator to be used
     *
     * @var  stubSkinGenerator
     */
    protected $skinGenerator;
    /**
     * name of skin to be used
     *
     * @var  stubSkinGenerator
     */
    protected $skinName       = 'default';
    /**
     * xsl processor to transform xml to html
     *
     * @var  stubXSLProcessor
     */
    protected $xslProcessor;
    /**
     * locale for output
     *
     * @var  string
     */
    protected $locale;
    /**
     * path to store profile log data in
     *
     * @var  string
     */
    protected $logPath;
    /**
     * uri of processor
     *
     * @var  string
     */
    protected $processorUri      = '/';

    /**
     * constructor
     *
     * @param  stubXSLProcessor   $xslProcessor
     * @param  stubSkinGenerator  $skinGenerator
     * @param  string             $locale
     * @Inject
     * @Named{locale}('net.stubbles.locale')
     */
    public function __construct(stubXSLProcessor $xslProcessor, stubSkinGenerator $skinGenerator, $locale)
    {
        $this->xslProcessor  = $xslProcessor;
        $this->skinGenerator = $skinGenerator;
        $this->locale        = $locale;
    }

    /**
     * select skin based on request and route
     *
     * Skin is selected based on the following rules:
     * 1. If $requestSkinName is not empty and skin with this name exists use this.
     * 2. If $routeSkinName is not empty and skin with this name exists use this.
     * 3. Use 'default' as skin.
     *
     * @param  stubRequest                  $requestSkinName
     * @param  string                       $routeSkinName
     * @return stubXmlProcessorTransformer
     */
    public function selectSkin($requestSkinName, $routeSkinName)
    {
        if (null != $requestSkinName && $this->skinGenerator->hasSkin($requestSkinName) === true) {
            $this->skinName = $requestSkinName;
            return $this;
        }

        if (null != $routeSkinName && $this->skinGenerator->hasSkin($routeSkinName) === true) {
            $this->skinName = $routeSkinName;
        }

        return $this;
    }

    /**
     * returns name of selected skin
     *
     * @return  string
     */
    public function getSelectedSkinName()
    {
        return $this->skinName;
    }

    /**
     * selects locale
     *
     * Locale is selected based on the following rules:
     * 1. If $sessionLocale is not empty use this.
     * 2. If $routeLocale is not empty use this.
     * 3. Use locale configured in config.ini
     *
     * @param   string                       $sessionLocale
     * @param   string                       $routeLocale
     * @return  stubXmlProcessorTransformer
     */
    public function selectLocale($sessionLocale, $routeLocale)
    {
        if (null != $sessionLocale) {
            $this->locale = $sessionLocale;
            return $this;
        }

        if (null != $routeLocale) {
            $this->locale = $routeLocale;
        }

        return $this;
    }

    /**
     * returns selected locale
     *
     * @return  string
     */
    public function getSelectedLocale()
    {
        return $this->locale;
    }

    /**
     *
     * @param   string                       $processorUri
     * @return  stubXmlProcessorTransformer
     * @since   1.7.0
     */
    public function setProcessorUri($processorUri)
    {
        $this->processorUri = $processorUri;
        return $this;
    }

    /**
     * transforms given xml stream to target format
     *
     * @param   stubXMLStreamWriter  $xmlStreamWriter
     * @param   string               $routeName
     * @return  string
     */
    public function transform(stubXMLStreamWriter $xmlStreamWriter, $routeName)
    {
        return str_replace(' xmlns=""',
                           '',
                           preg_replace('/ xml:base="(.*)"/U',
                                        '',
                                        $this->xslProcessor->applyStylesheet($this->skinGenerator->generate($routeName,
                                                                                                            $this->skinName,
                                                                                                            $this->locale,
                                                                                                            $this->processorUri
                                                                                                   )
                                                             )
                                                           ->onDocument($xmlStreamWriter->asDOM())
                                                           ->toXml()
                           )
               );
    }
}
?>