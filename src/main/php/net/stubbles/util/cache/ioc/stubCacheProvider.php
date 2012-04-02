<?php
/**
 * Provider for cache containers.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc
 * @version     $Id: stubCacheProvider.php 2490 2010-01-25 20:53:20Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::util::cache::stubCacheStrategy',
                      'net::stubbles::util::cache::stubFileCacheContainer'
);
/**
 * Provider for cache containers.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc
 */
class stubCacheProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * default name
     */
    const DEFAULT_NAME        = '__default';
    /**
     * default cache strategy to be used
     *
     * @var  stubCacheStrategy
     */
    protected $cacheStrategy;
    /**
     * directory to store cache files
     *
     * @var  string
     */
    protected $cachePath;
    /**
     * list of available cache containers
     *
     * @var  array<string,stubCacheContainer>
     */
    protected $cacheContainer = array();
    /**
     * mode for new files and directories
     *
     * @var  int
     */
    protected $fileMode       = 0700;

    /**
     * constructor
     *
     * Please make sure that the given directory does exist.
     *
     * @param  stubCacheStrategy  $strategy   strategy regarding caching
     * @param  string             $cachePath  where to store cache files
     * @Inject
     * @Named{cachePath}('net.stubbles.cache.path')
     */
    public function __construct(stubCacheStrategy $strategy, $cachePath)
    {
        $this->strategy  = $strategy;
        $this->cachePath = $cachePath;
    }

    /**
     * sets the mode for new files and directories
     *
     * @param   int                $fileMode
     * @return  stubCacheProvider
     * @Inject(optional=true)
     * @Named('net.stubbles.util.cache.filemode')
     */
    public function setFileMode($fileMode)
    {
        $this->fileMode = $fileMode;
        return $this;
    }

    /**
     * returns the requested cache container
     *
     * If no special cache container is requested or the cache container with
     * the requested name does not exist it will try to return the default
     * cache container.
     *
     * @param   string              $name  optional  name of requested cache container
     * @return  stubCacheContainer
     */
    public function get($name = null)
    {
        if (null == $name) {
            $name      = self::DEFAULT_NAME;
            $cachePath = $this->cachePath;
        } else {
            $cachePath = $this->cachePath . DIRECTORY_SEPARATOR . $name;
        }
        
        if (isset($this->cacheContainer[$name]) === false) {
            $this->cacheContainer[$name] = new stubFileCacheContainer($this->strategy,
                                                                      $cachePath,
                                                                      $this->fileMode
                                           );
        }
        
        return $this->cacheContainer[$name]->gc();
    }
}
?>