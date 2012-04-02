<?php
/**
 * Skin generator that uses another skin generator and caches its results.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin
 * @version     $Id: stubCachingSkinGenerator.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::util::cache::stubCacheContainer',
                      'net::stubbles::webapp::xml::skin::stubSkinGenerator'
);
/**
 * Skin generator that uses another skin generator and caches its results.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin
 */
class stubCachingSkinGenerator extends stubBaseObject implements stubSkinGenerator
{
    /**
     * real skin generator to be used
     *
     * @var  stubSkinGenerator
     */
    protected $skinGenerator;
    /**
     * cache to be used
     *
     * @var  stubCacheContainer
     */
    protected $cache;

    /**
     * constructor
     *
     * @param  stubSkinGenerator   $skinGenerator  real skin generator to be used
     * @param  stubCacheContainer  $cache          cache to be used
     * @Inject
     * @Named{skinGenerator}('webapp.xml.skin.default')
     * @Named{cache}('skin')
     */
    public function __construct(stubSkinGenerator $skinGenerator, stubCacheContainer $cache)
    {
        $this->skinGenerator = $skinGenerator;
        $this->cache         = $cache;
    }

    /**
     * checks whether a given skin exists
     *
     * @param   string  $skinName
     * @return  bool
     */
    public function hasSkin($skinName)
    {
        return $this->skinGenerator->hasSkin($skinName);
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
        $key = md5($routeName . $skinName . $locale);
        if ($this->cache->has($key) === true) {
            $resultXSL = new DOMDocument();
            $resultXSL->loadXML($this->cache->get($key));
            return $resultXSL;
        }
        
        $resultXSL = $this->skinGenerator->generate($routeName, $skinName, $locale, $processorUri);
        $this->cache->put($key, $resultXSL->saveXML());
        return $resultXSL;
    }
}
?>